<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\Subject;
use App\Models\Chapter;
use App\Models\Lesson;
use App\Models\Exam;
use App\Models\ExamSection;
use App\Models\ExamQuestion;
use App\Models\ExamQuestionOption;
use App\Services\NotificationService;

class ExamController extends Controller
{
    private function authorizeCourse(Course $course)
    {
        $teacher = Auth::user();
        if (!$course->teachers()->where('teacher_id', $teacher->id)->exists() && !$teacher->isTenantAdmin()) {
            abort(403, 'You are not assigned to this course.');
        }
    }

    public function index(Course $course)
    {
        $this->authorizeCourse($course);
        $exams = $course->exams()->with(['subject', 'chapter', 'lesson'])->latest()->paginate(20);
        return view('teacher.exams.index', compact('course', 'exams'));
    }

    public function create(Course $course, Request $request)
    {
        $this->authorizeCourse($course);

        $level = $request->get('level');
        $levelId = $request->get('level_id');

        $subject = null;
        $chapter = null;
        $lesson = null;

        if ($level === 'subject' && $levelId) {
            $subject = Subject::find($levelId);
        } elseif ($level === 'chapter' && $levelId) {
            $chapter = Chapter::find($levelId);
            $subject = $chapter?->subject;
        } elseif ($level === 'lesson' && $levelId) {
            $lesson = Lesson::find($levelId);
            $chapter = $lesson?->chapter;
            $subject = $chapter?->subject;
        }

        return view('teacher.exams.create', compact('course', 'level', 'levelId', 'subject', 'chapter', 'lesson'));
    }

    public function store(Request $request, Course $course)
    {
        $this->authorizeCourse($course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'template' => 'required|in:default',
            'level' => 'required|in:course,subject,chapter,lesson',
            'subject_id' => 'nullable|exists:subjects,id',
            'chapter_id' => 'nullable|exists:chapters,id',
            'lesson_id' => 'nullable|exists:lessons,id',
            'duration_minutes' => 'nullable|integer|min:1',
            'passing_percentage' => 'nullable|integer|min:0|max:100',
            'allow_multiple_attempts' => 'boolean',
            'max_attempts' => 'nullable|integer|min:1',
            'shuffle_questions' => 'boolean',
            'show_result_immediately' => 'boolean',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
        ]);

        $exam = new Exam([
            'tenant_id' => Auth::user()->tenant_id,
            'course_id' => $course->id,
            'created_by' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'template' => $validated['template'],
            'status' => 'draft',
            'duration_minutes' => $validated['duration_minutes'] ?? null,
            'passing_marks' => 0,
            'allow_multiple_attempts' => $validated['allow_multiple_attempts'] ?? false,
            'max_attempts' => $validated['max_attempts'] ?? null,
            'shuffle_questions' => $validated['shuffle_questions'] ?? false,
            'show_result_immediately' => $validated['show_result_immediately'] ?? true,
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
        ]);

        if ($validated['level'] === 'subject') {
            $exam->subject_id = $validated['subject_id'];
        } elseif ($validated['level'] === 'chapter') {
            $exam->subject_id = $validated['subject_id'];
            $exam->chapter_id = $validated['chapter_id'];
        } elseif ($validated['level'] === 'lesson') {
            $exam->subject_id = $validated['subject_id'];
            $exam->chapter_id = $validated['chapter_id'];
            $exam->lesson_id = $validated['lesson_id'];
        }

        $exam->save();

        if (!empty($validated['passing_percentage'])) {
            session(['exam_' . $exam->id . '_passing_percentage' => $validated['passing_percentage']]);
        }

        return redirect()->route('teacher.exams.questions.create', [$course, $exam])
            ->with('success', 'Exam created successfully! Now add questions.');
    }

    public function show(Course $course, Exam $exam)
    {
        $this->authorizeCourse($course);
        $exam->load(['sections.questions.options', 'questions.options', 'creator']);
        return view('teacher.exams.show', compact('course', 'exam'));
    }

    public function createQuestions(Course $course, Exam $exam)
    {
        $this->authorizeCourse($course);
        $exam->load(['sections', 'questions.options']);
        return view('teacher.exams.create_questions', compact('course', 'exam'));
    }

    public function storeQuestions(Request $request, Course $course, Exam $exam)
    {
        $this->authorizeCourse($course);

        $validated = $request->validate([
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string',
            'questions.*.question_type' => 'required|in:single_choice,multiple_choice,true_false',
            'questions.*.marks' => 'required|integer|min:1',
            'questions.*.negative_marks' => 'nullable|numeric|min:0',
            'questions.*.section_id' => 'nullable|exists:exam_sections,id',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.options.*.option_text' => 'required|string',
            'questions.*.options.*.is_correct' => 'boolean',
            'questions.*.explanation' => 'nullable|string',
        ]);

        $order = $exam->questions()->count();

        foreach ($validated['questions'] as $qData) {
            $order++;
            $question = ExamQuestion::create([
                'exam_id' => $exam->id,
                'section_id' => $qData['section_id'] ?? null,
                'question_text' => $qData['question_text'],
                'question_type' => $qData['question_type'],
                'marks' => $qData['marks'],
                'negative_marks' => $qData['negative_marks'] ?? 0,
                'explanation' => $qData['explanation'] ?? null,
                'order' => $order,
            ]);

            foreach ($qData['options'] as $index => $oData) {
                ExamQuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $oData['option_text'],
                    'is_correct' => $oData['is_correct'] ?? false,
                    'order' => $index + 1,
                ]);
            }
        }

        $exam->recalculateTotalMarks();

        $passingPercentage = session('exam_' . $exam->id . '_passing_percentage');
        if ($passingPercentage) {
            $exam->passing_marks = ($exam->total_marks * $passingPercentage) / 100;
            $exam->save();
            session()->forget('exam_' . $exam->id . '_passing_percentage');
        }

        return redirect()->route('teacher.exams.show', [$course, $exam])
            ->with('success', 'Questions added successfully!');
    }

    public function storeSection(Request $request, Course $course, Exam $exam)
    {
        $this->authorizeCourse($course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'marks_per_question' => 'nullable|integer|min:1',
            'negative_marks_per_question' => 'nullable|numeric|min:0',
            'time_limit_minutes' => 'nullable|integer|min:1',
        ]);

        ExamSection::create([
            'exam_id' => $exam->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'order' => $exam->sections()->count() + 1,
            'marks_per_question' => $validated['marks_per_question'] ?? 1,
            'negative_marks_per_question' => $validated['negative_marks_per_question'] ?? 0,
            'time_limit_minutes' => $validated['time_limit_minutes'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Section added successfully!');
    }

    public function publish(Course $course, Exam $exam)
    {
        $this->authorizeCourse($course);

        if ($exam->questions()->count() === 0) {
            return redirect()->back()->with('error', 'Cannot publish exam without questions!');
        }

        $exam->status = 'published';
        $exam->save();

        try {
            $exam->load('course');
            (new NotificationService())->examPublished(Auth::user()->tenant, $exam);
        } catch (\Throwable $e) {
            \Log::warning('Exam notification failed: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Exam published successfully!');
    }

    public function destroy(Course $course, Exam $exam)
    {
        $this->authorizeCourse($course);
        $exam->delete();
        return redirect()->route('teacher.courses.show', $course)
            ->with('success', 'Exam deleted successfully!');
    }

    public function importQuestions(Request $request, Course $course, Exam $exam)
    {
        $this->authorizeCourse($course);

        $validated = $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
            'default_marks' => 'required|integer|min:1',
            'default_negative_marks' => 'nullable|numeric|min:0',
            'section_id' => 'nullable|exists:exam_sections,id',
        ]);

        $sectionId = $validated['section_id'] ?? null;
        $file = $request->file('csv_file');
        $path = $file->getRealPath();

        $questions = [];
        $row = 0;

        if (($handle = fopen($path, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $row++;
                if ($row === 1) continue;
                if (empty($data[0]) || trim($data[0]) === '') continue;
                if (count($data) < 6) continue;

                $correctOption = (int) trim($data[5]);
                if ($correctOption < 1 || $correctOption > 4) continue;

                $questions[] = [
                    'question_text' => trim($data[0]),
                    'options' => [trim($data[1]), trim($data[2]), trim($data[3]), trim($data[4])],
                    'correct_option' => $correctOption,
                ];
            }
            fclose($handle);
        }

        if (empty($questions)) {
            return redirect()->back()->with('error', 'No valid questions found in CSV file.');
        }

        $startingOrder = $exam->questions()->count();

        foreach ($questions as $qData) {
            $startingOrder++;
            $question = ExamQuestion::create([
                'exam_id' => $exam->id,
                'section_id' => $sectionId,
                'question_text' => $qData['question_text'],
                'question_type' => 'single_choice',
                'marks' => $validated['default_marks'],
                'negative_marks' => $validated['default_negative_marks'] ?? 0,
                'order' => $startingOrder,
            ]);

            foreach ($qData['options'] as $index => $optionText) {
                ExamQuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $optionText,
                    'is_correct' => ($index + 1) === $qData['correct_option'],
                    'order' => $index + 1,
                ]);
            }
        }

        $exam->recalculateTotalMarks();

        return redirect()->route('teacher.exams.show', [$course, $exam])
            ->with('success', count($questions) . ' questions imported successfully!');
    }

    public function downloadTemplate(Course $course, Exam $exam)
    {
        $this->authorizeCourse($course);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="exam_questions_template.csv"',
        ];

        $csvContent = "question,option1,option2,option3,option4,correct_option\n";
        $csvContent .= "What is the capital of France?,London,Berlin,Paris,Madrid,3\n";

        return response($csvContent, 200, $headers);
    }
}
