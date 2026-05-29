<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    /**
     * Display a listing of available exams for the student
     */
    public function index()
    {
        $student = Auth::user();
        
        // Get course IDs the student is enrolled in
        $courseIds = $student->enrollments()
            ->whereIn('enrollment_status', ['active', 'completed'])
            ->pluck('course_id');
        
        // Get published exams from enrolled courses (including past exams)
        $availableExams = Exam::whereIn('course_id', $courseIds)
            ->where('status', 'published')
            ->where(function($q) {
                $q->whereNull('start_time')
                  ->orWhere('start_time', '<=', now());
            })
            ->with(['course', 'sections'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get student's previous attempts
        $myAttempts = ExamAttempt::where('user_id', $student->id)
            ->with(['exam', 'exam.course'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('student.exams.index', compact('availableExams', 'myAttempts'));
    }

    /**
     * Display the specified exam details
     */
    public function show(Exam $exam)
    {
        $student = Auth::user();
        
        // Check if student is enrolled in this course
        $enrollment = $student->enrollments()
            ->where('course_id', $exam->course_id)
            ->whereIn('enrollment_status', ['active', 'completed'])
            ->first();
        
        if (!$enrollment) {
            return redirect()->route('student.dashboard')
                ->with('error', 'You need to enroll in this course to access exams.');
        }
        
        // Check if exam is published and active
        if ($exam->status !== 'published') {
            return redirect()->route('student.dashboard')
                ->with('error', 'This exam is not available yet.');
        }
        
        if ($exam->start_time && $exam->start_time > now()) {
            return redirect()->route('student.dashboard')
                ->with('error', 'This exam has not started yet.');
        }
        
        if ($exam->end_time && $exam->end_time < now()) {
            return redirect()->route('student.dashboard')
                ->with('error', 'This exam has ended.');
        }
        
        $exam->load(['sections.questions.options', 'questions.options']);
        
        // Check for existing attempts
        $existingAttempts = ExamAttempt::where('user_id', $student->id)
            ->where('exam_id', $exam->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Check if student can attempt again
        $canAttempt = true;
        $attemptMessage = '';
        
        if ($exam->allow_multiple_attempts) {
            $maxAttempts = $exam->max_attempts ?? PHP_INT_MAX;
            if ($existingAttempts->count() >= $maxAttempts) {
                $canAttempt = false;
                $attemptMessage = 'You have used all available attempts.';
            }
        } else {
            if ($existingAttempts->where('status', 'completed')->count() > 0) {
                $canAttempt = false;
                $attemptMessage = 'You have already completed this exam.';
            }
        }
        
        return view('student.exams.show', compact('exam', 'existingAttempts', 'canAttempt', 'attemptMessage'));
    }

    /**
     * Start a new exam attempt
     */
    public function startAttempt(Exam $exam)
    {
        $student = Auth::user();
        
        // Check enrollment
        $enrollment = $student->enrollments()
            ->where('course_id', $exam->course_id)
            ->whereIn('enrollment_status', ['active', 'completed'])
            ->first();
        
        if (!$enrollment) {
            return redirect()->route('student.dashboard')
                ->with('error', 'You need to enroll in this course to access exams.');
        }
        
        // Check if exam is active
        if ($exam->status !== 'published') {
            return redirect()->route('student.exams.show', $exam)
                ->with('error', 'This exam is not available.');
        }
        
        // Check attempts
        if (!$exam->allow_multiple_attempts) {
            $existingCompleted = ExamAttempt::where('user_id', $student->id)
                ->where('exam_id', $exam->id)
                ->where('status', 'completed')
                ->exists();
            
            if ($existingCompleted) {
                return redirect()->route('student.exams.show', $exam)
                    ->with('error', 'You have already completed this exam.');
            }
        } else {
            $maxAttempts = $exam->max_attempts ?? PHP_INT_MAX;
            $attemptCount = ExamAttempt::where('user_id', $student->id)
                ->where('exam_id', $exam->id)
                ->count();
            
            if ($attemptCount >= $maxAttempts) {
                return redirect()->route('student.exams.show', $exam)
                    ->with('error', 'You have used all available attempts.');
            }
        }
        
        // Create new attempt
        $attempt = ExamAttempt::create([
            'exam_id' => $exam->id,
            'user_id' => $student->id,
            'enrollment_id' => $enrollment->id,
            'started_at' => now(),
            'status' => 'in_progress',
        ]);
        
        return redirect()->route('student.exams.attempt', ['exam' => $exam, 'attempt' => $attempt]);
    }

    /**
     * Show the exam attempt page
     */
    public function attempt(Exam $exam, ExamAttempt $attempt)
    {
        $student = Auth::user();
        
        // Verify ownership
        if ($attempt->user_id !== $student->id || $attempt->exam_id !== $exam->id) {
            abort(403, 'Unauthorized access.');
        }
        
        // Check if attempt is still in progress
        if ($attempt->status !== 'in_progress') {
            return redirect()->route('student.exams.results', ['exam' => $exam, 'attempt' => $attempt])
                ->with('info', 'This attempt has already been completed.');
        }
        
        // Check time limit
        if ($exam->duration_minutes && $attempt->hasTimeExpired()) {
            // Auto-submit
            $this->submitAttempt($exam, $attempt);
            return redirect()->route('student.exams.results', ['exam' => $exam, 'attempt' => $attempt])
                ->with('info', 'Time expired. Your exam has been auto-submitted.');
        }
        
        $exam->load(['sections.questions.options', 'questions.options']);
        
        // Get previously saved answers
        $savedAnswers = ExamAnswer::where('attempt_id', $attempt->id)
            ->pluck('selected_option_id', 'question_id')
            ->toArray();
        
        $timeRemaining = $exam->duration_minutes 
            ? $attempt->time_remaining 
            : null;
        
        return view('student.exams.attempt', compact('exam', 'attempt', 'savedAnswers', 'timeRemaining'));
    }

    /**
     * Save an answer during the exam
     */
    public function saveAnswer(Request $request, Exam $exam, ExamAttempt $attempt)
    {
        $student = Auth::user();
        
        if ($attempt->user_id !== $student->id || $attempt->exam_id !== $exam->id) {
            abort(403);
        }
        
        if ($attempt->status !== 'in_progress') {
            return response()->json(['error' => 'Attempt already completed'], 400);
        }
        
        $validated = $request->validate([
            'question_id' => 'required|exists:exam_questions,id',
            'option_id' => 'nullable|exists:exam_question_options,id',
        ]);
        
        // Check time limit
        if ($exam->duration_minutes && $attempt->hasTimeExpired()) {
            return response()->json(['error' => 'Time expired'], 400);
        }
        
        // Save or update answer
        ExamAnswer::updateOrCreate(
            [
                'attempt_id' => $attempt->id,
                'question_id' => $validated['question_id'],
            ],
            [
                'selected_option_id' => $validated['option_id'],
                'answered_at' => now(),
            ]
        );
        
        return response()->json(['success' => true]);
    }

    /**
     * Submit the exam attempt
     */
    public function submit(Request $request, Exam $exam, ExamAttempt $attempt)
    {
        $student = Auth::user();
        
        if ($attempt->user_id !== $student->id || $attempt->exam_id !== $exam->id) {
            abort(403);
        }
        
        if ($attempt->status !== 'in_progress') {
            return redirect()->route('student.exams.results', ['exam' => $exam, 'attempt' => $attempt]);
        }
        
        $this->submitAttempt($exam, $attempt);
        
        return redirect()->route('student.exams.results', ['exam' => $exam, 'attempt' => $attempt])
            ->with('success', 'Exam submitted successfully!');
    }

    /**
     * Show exam results
     */
    public function results(Exam $exam, ExamAttempt $attempt)
    {
        $student = Auth::user();
        
        if ($attempt->user_id !== $student->id || $attempt->exam_id !== $exam->id) {
            abort(403);
        }
        
        $attempt->load(['answers.question.options', 'answers.selectedOption']);
        $exam->load(['sections.questions.options', 'questions.options']);
        
        // Calculate score if not already done
        if ($attempt->score === null) {
            $attempt->calculateScore();
        }
        
        return view('student.exams.results', compact('exam', 'attempt'));
    }

    /**
     * Helper method to submit an attempt
     */
    private function submitAttempt(Exam $exam, ExamAttempt $attempt)
    {
        $attempt->update([
            'status' => 'completed',
            'submitted_at' => now(),
        ]);
        
        // Grade all answers and calculate score
        foreach ($attempt->answers as $answer) {
            $answer->grade();
        }
        
        $attempt->calculateScore();
    }
}
