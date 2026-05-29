<?php

namespace App\Http\Controllers\Tenant;

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
use Illuminate\Support\Facades\Validator;

class ExamController extends Controller
{
    // Show form to create exam with pre-selected level
    public function create(Course $course, Request $request)
    {
        $this->authorizeCourse($course);
        
        // Get level info from query params
        $level = $request->get('level'); // subject, chapter, lesson
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
        
        return view('tenant.exams.create', compact(
            'course', 'level', 'levelId', 'subject', 'chapter', 'lesson'
        ));
    }

    // Store new exam
    public function store(Request $request, Course $course)
    {
        $this->authorizeCourse($course);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'template' => 'required|in:default',
            'level' => 'required|in:course,subject,chapter,lesson',
            'subject_id' => 'nullable|required_if:level,subject,chapter,lesson|exists:subjects,id',
            'chapter_id' => 'nullable|required_if:level,chapter,lesson|exists:chapters,id',
            'lesson_id' => 'nullable|required_if:level,lesson|exists:lessons,id',
            'duration_minutes' => 'nullable|integer|min:1',
            'passing_percentage' => 'nullable|integer|min:0|max:100',
            'allow_multiple_attempts' => 'boolean',
            'max_attempts' => 'nullable|integer|min:1',
            'shuffle_questions' => 'boolean',
            'show_result_immediately' => 'boolean',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
        ]);

        // Create exam
        $exam = new Exam([
            'tenant_id' => Auth::user()->tenant_id,
            'course_id' => $course->id,
            'created_by' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'template' => $validated['template'],
            'status' => 'draft',
            'duration_minutes' => $validated['duration_minutes'] ?? null,
            'passing_marks' => 0, // Will be calculated based on questions
            'allow_multiple_attempts' => $validated['allow_multiple_attempts'] ?? false,
            'max_attempts' => $validated['max_attempts'] ?? null,
            'shuffle_questions' => $validated['shuffle_questions'] ?? false,
            'show_result_immediately' => $validated['show_result_immediately'] ?? true,
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
        ]);

        // Set level IDs
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

        // If passing percentage provided, store it temporarily until questions are added
        if (!empty($validated['passing_percentage'])) {
            session(['exam_' . $exam->id . '_passing_percentage' => $validated['passing_percentage']]);
        }

        return redirect()->route('tenant.exams.questions.create', [$course, $exam])
            ->with('success', 'Exam created successfully! Now add questions and sections.');
    }

    // Show exam details and questions
    public function show(Course $course, Exam $exam)
    {
        $this->authorizeCourse($course);
        
        $exam->load(['sections.questions.options', 'questions.options', 'creator']);
        
        return view('tenant.exams.show', compact('course', 'exam'));
    }

    // Show form to add questions
    public function createQuestions(Course $course, Exam $exam)
    {
        $this->authorizeCourse($course);
        
        $exam->load(['sections', 'questions.options']);
        
        return view('tenant.exams.create_questions', compact('course', 'exam'));
    }

    // Store questions
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
            
            // Create question
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

            // Create options
            foreach ($qData['options'] as $index => $oData) {
                ExamQuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $oData['option_text'],
                    'is_correct' => $oData['is_correct'] ?? false,
                    'order' => $index + 1,
                ]);
            }
        }

        // Recalculate exam totals
        $exam->recalculateTotalMarks();

        // Apply passing percentage if stored
        $passingPercentage = session('exam_' . $exam->id . '_passing_percentage');
        if ($passingPercentage) {
            $exam->passing_marks = ($exam->total_marks * $passingPercentage) / 100;
            $exam->save();
            session()->forget('exam_' . $exam->id . '_passing_percentage');
        }

        return redirect()->route('tenant.exams.show', [$course, $exam])
            ->with('success', 'Questions added successfully!');
    }

    // Store section
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

        $order = $exam->sections()->count() + 1;

        $section = ExamSection::create([
            'exam_id' => $exam->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'order' => $order,
            'marks_per_question' => $validated['marks_per_question'] ?? 1,
            'negative_marks_per_question' => $validated['negative_marks_per_question'] ?? 0,
            'time_limit_minutes' => $validated['time_limit_minutes'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Section added successfully!');
    }

    // Publish exam
    public function publish(Course $course, Exam $exam)
    {
        $this->authorizeCourse($course);
        
        if ($exam->questions()->count() === 0) {
            return redirect()->back()->with('error', 'Cannot publish exam without questions!');
        }

        $exam->status = 'published';
        $exam->save();

        return redirect()->back()->with('success', 'Exam published successfully!');
    }

    // Delete exam
    public function destroy(Course $course, Exam $exam)
    {
        $this->authorizeCourse($course);
        
        $exam->delete();

        return redirect()->route('tenant.curriculum.index', $course)
            ->with('success', 'Exam deleted successfully!');
    }

    // List all exams for a course
    public function index(Course $course)
    {
        $this->authorizeCourse($course);
        
        $exams = $course->exams()->with(['subject', 'chapter', 'lesson'])->latest()->paginate(20);
        
        return view('tenant.exams.index', compact('course', 'exams'));
    }

    // Download CSV Template
    public function downloadTemplate(Course $course, Exam $exam)
    {
        $this->authorizeCourse($course);
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="exam_questions_template.csv"',
        ];

        $csvContent = "question,option1,option2,option3,option4,correct_option\n";
        $csvContent .= "What is the capital of France?,London,Berlin,Paris,Madrid,3\n";
        $csvContent .= "What is 2+2?,2,3,4,5,3\n";
        $csvContent .= "Which planet is closest to the Sun?,Venus,Earth,Mars,Mercury,4\n";

        return response($csvContent, 200, $headers);
    }

    // Import Questions from CSV
    public function importQuestions(Request $request, Course $course, Exam $exam)
    {
        $this->authorizeCourse($course);

        try {
            $validated = $request->validate([
                'csv_file' => 'required|file|mimes:csv,txt',
                'default_marks' => 'required|integer|min:1',
                'default_negative_marks' => 'nullable|numeric|min:0',
                'section_id' => 'nullable|exists:exam_sections,id',
            ]);

            $sectionId = $validated['section_id'] ?? null;

            $file = $request->file('csv_file');
            $path = $file->getRealPath();
            
            // Debug: log file info
            \Log::info('CSV Import started', [
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'section_id' => $sectionId,
                'exam_id' => $exam->id
            ]);

        // Read CSV
        $questions = [];
        $row = 0;
        $debugInfo = [];
        
        if (($handle = fopen($path, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $row++;
                $debugInfo[] = "Row {$row}: " . json_encode($data);
                
                // Skip header row
                if ($row === 1) {
                    // Validate headers - be more lenient
                    $headers = array_map('strtolower', array_map('trim', $data));
                    $debugInfo[] = "Headers found: " . json_encode($headers);
                    
                    // Check if at least has question and some options
                    if (!in_array('question', $headers)) {
                        fclose($handle);
                        return redirect()->back()->with('error', 'CSV must have a "question" column. Found headers: ' . implode(', ', $headers));
                    }
                    continue;
                }

                // Skip empty rows
                if (empty($data[0]) || trim($data[0]) === '') {
                    $debugInfo[] = "Row {$row} skipped (empty question)";
                    continue;
                }

                // Validate data - need at least 2 columns (question + 1 option)
                if (count($data) < 2) {
                    fclose($handle);
                    return redirect()->back()->with('error', "Row {$row} has insufficient data. Found " . count($data) . " columns.");
                }

                $questionText = trim($data[0]);
                $options = [
                    trim($data[1]),
                    trim($data[2]),
                    trim($data[3]),
                    trim($data[4])
                ];
                $correctOption = (int) trim($data[5]);

                // Validate correct option
                if ($correctOption < 1 || $correctOption > 4) {
                    fclose($handle);
                    return redirect()->back()->with('error', "Invalid correct_option in row {$row}. Must be 1, 2, 3, or 4.");
                }

                // Check for empty options (only check first 2 as minimum)
                for ($i = 0; $i < 2; $i++) {
                    if (empty($options[$i]) || trim($options[$i]) === '') {
                        fclose($handle);
                        return redirect()->back()->with('error', "Empty option " . ($i + 1) . " in row {$row}.");
                    }
                }

                $questions[] = [
                    'question_text' => $questionText,
                    'options' => $options,
                    'correct_option' => $correctOption,
                ];
            }
            fclose($handle);
        }

        if (empty($questions)) {
            // Store debug info in session for troubleshooting
            session(['csv_debug' => implode("\n", array_slice($debugInfo, 0, 20))]);
            return redirect()->back()->with('error', 'No valid questions found in CSV file. Total rows processed: ' . ($row - 1));
        }

        // Get starting order
        $startingOrder = $exam->questions()->count();

        // Create questions
        foreach ($questions as $qData) {
            $startingOrder++;

            $question = ExamQuestion::create([
                'exam_id' => $exam->id,
                'section_id' => $sectionId, // Assigned to specific section if provided
                'question_text' => $qData['question_text'],
                'question_type' => 'single_choice',
                'marks' => $validated['default_marks'],
                'negative_marks' => $validated['default_negative_marks'] ?? 0,
                'explanation' => null,
                'order' => $startingOrder,
            ]);

            // Create options
            foreach ($qData['options'] as $index => $optionText) {
                ExamQuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $optionText,
                    'is_correct' => ($index + 1) === $qData['correct_option'],
                    'order' => $index + 1,
                ]);
            }
        }

        // Recalculate exam totals
        $exam->recalculateTotalMarks();

        // Update section question count and get section name
        $sectionName = '';
        if ($sectionId) {
            $section = ExamSection::find($sectionId);
            if ($section) {
                $section->total_questions = $section->questions()->count();
                $section->save();
                $sectionName = $section->title;
            }
        }
        
        $message = count($questions) . ' questions imported successfully';
        if ($sectionName) {
            $message .= ' to section "' . $sectionName . '"';
        }
        $message .= ' from CSV!';

        \Log::info('CSV Import completed', ['questions_imported' => count($questions)]);

        return redirect()->route('tenant.exams.show', [$course, $exam])
            ->with('success', $message);
            
        } catch (\Exception $e) {
            \Log::error('CSV Import failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    // Helper method
    private function authorizeCourse(Course $course)
    {
        if ($course->tenant_id !== Auth::user()->tenant_id) {
            abort(403, 'Unauthorized access to this course.');
        }
    }
}
