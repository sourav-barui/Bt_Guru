<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\Curriculum;
use App\Models\Subject;
use App\Models\Chapter;
use App\Models\Lesson;
use App\Models\CurriculumContent;
use App\Models\CurriculumNote;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Services\NotificationService;

class CurriculumController extends Controller
{
    // ==================== CURRICULUM ====================
    
    public function index(Course $course)
    {
        $this->authorizeCourse($course);
        
        $course->load(['curricula' => function ($q) {
            $q->with([
                'subjects.contents.user',
                'subjects.notes.user',
                'subjects.liveClasses.creator',
                'subjects.chapters.contents.user',
                'subjects.chapters.notes.user',
                'subjects.chapters.liveClasses.creator',
                'subjects.chapters.lessons.contents.user',
                'subjects.chapters.lessons.notes.user',
                'subjects.chapters.lessons.liveClasses.creator',
            ]);
        }]);
        
        return view('tenant.curriculum.index', compact('course'));
    }

    public function createCurriculum(Course $course)
    {
        $this->authorizeCourse($course);
        return view('tenant.curriculum.create_curriculum', compact('course'));
    }

    public function storeCurriculum(Request $request, Course $course)
    {
        $this->authorizeCourse($course);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive,draft',
        ]);
        
        $validated['course_id'] = $course->id;
        
        Curriculum::create($validated);
        
        return redirect()->route('tenant.curriculum.index', $course)
            ->with('success', 'Curriculum section created successfully.');
    }

    public function editCurriculum(Course $course, Curriculum $curriculum)
    {
        $this->authorizeCourse($course);
        return view('tenant.curriculum.edit_curriculum', compact('course', 'curriculum'));
    }

    public function updateCurriculum(Request $request, Course $course, Curriculum $curriculum)
    {
        $this->authorizeCourse($course);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive,draft',
        ]);
        
        $curriculum->update($validated);
        
        return redirect()->route('tenant.curriculum.index', $course)
            ->with('success', 'Curriculum section updated successfully.');
    }

    public function destroyCurriculum(Course $course, Curriculum $curriculum)
    {
        $this->authorizeCourse($course);
        $curriculum->delete();
        
        return redirect()->route('tenant.curriculum.index', $course)
            ->with('success', 'Curriculum section deleted successfully.');
    }

    // ==================== SUBJECT ====================
    
    public function createSubject(Course $course, Curriculum $curriculum)
    {
        $this->authorizeCourse($course);
        return view('tenant.curriculum.create_subject', compact('course', 'curriculum'));
    }

    public function storeSubject(Request $request, Course $course, Curriculum $curriculum)
    {
        $this->authorizeCourse($course);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive,draft',
        ]);
        
        $validated['curriculum_id'] = $curriculum->id;
        
        Subject::create($validated);
        
        return redirect()->route('tenant.curriculum.index', $course)
            ->with('success', 'Subject created successfully.');
    }

    public function editSubject(Course $course, Subject $subject)
    {
        $this->authorizeCourse($course);
        return view('tenant.curriculum.edit_subject', compact('course', 'subject'));
    }

    public function updateSubject(Request $request, Course $course, Subject $subject)
    {
        $this->authorizeCourse($course);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive,draft',
        ]);
        
        $subject->update($validated);
        
        return redirect()->route('tenant.curriculum.index', $course)
            ->with('success', 'Subject updated successfully.');
    }

    public function destroySubject(Course $course, Subject $subject)
    {
        $this->authorizeCourse($course);
        $subject->delete();
        
        return redirect()->route('tenant.curriculum.index', $course)
            ->with('success', 'Subject deleted successfully.');
    }

    // ==================== CHAPTER ====================
    
    public function createChapter(Course $course, Subject $subject)
    {
        $this->authorizeCourse($course);
        return view('tenant.curriculum.create_chapter', compact('course', 'subject'));
    }

    public function storeChapter(Request $request, Course $course, Subject $subject)
    {
        $this->authorizeCourse($course);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive,draft',
        ]);
        
        $validated['subject_id'] = $subject->id;
        
        Chapter::create($validated);
        
        return redirect()->route('tenant.curriculum.index', $course)
            ->with('success', 'Chapter created successfully.');
    }

    public function editChapter(Course $course, Chapter $chapter)
    {
        $this->authorizeCourse($course);
        return view('tenant.curriculum.edit_chapter', compact('course', 'chapter'));
    }

    public function updateChapter(Request $request, Course $course, Chapter $chapter)
    {
        $this->authorizeCourse($course);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive,draft',
        ]);
        
        $chapter->update($validated);
        
        return redirect()->route('tenant.curriculum.index', $course)
            ->with('success', 'Chapter updated successfully.');
    }

    public function destroyChapter(Course $course, Chapter $chapter)
    {
        $this->authorizeCourse($course);
        $chapter->delete();
        
        return redirect()->route('tenant.curriculum.index', $course)
            ->with('success', 'Chapter deleted successfully.');
    }

    // ==================== LESSON ====================
    
    public function createLesson(Course $course, Chapter $chapter)
    {
        $this->authorizeCourse($course);
        return view('tenant.curriculum.create_lesson', compact('course', 'chapter'));
    }

    public function storeLesson(Request $request, Course $course, Chapter $chapter)
    {
        $this->authorizeCourse($course);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'nullable|url',
            'video_type' => 'required_with:video_url|in:youtube,vimeo,other',
            'duration_minutes' => 'nullable|integer|min:1',
            'order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive,draft',
        ]);
        
        $validated['chapter_id'] = $chapter->id;

        $lesson = Lesson::create($validated);

        // Notify students about new video/lesson
        try {
            (new NotificationService())->lessonAdded(Auth::user()->tenant, $lesson);
        } catch (\Throwable $e) {
            \Log::warning('Lesson notification failed: ' . $e->getMessage());
        }

        return redirect()->route('tenant.curriculum.index', $course)
            ->with('success', 'Lesson created successfully.');
    }

    public function editLesson(Course $course, Lesson $lesson)
    {
        $this->authorizeCourse($course);
        return view('tenant.curriculum.edit_lesson', compact('course', 'lesson'));
    }

    public function updateLesson(Request $request, Course $course, Lesson $lesson)
    {
        $this->authorizeCourse($course);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'nullable|url',
            'video_type' => 'required_with:video_url|in:youtube,vimeo,other',
            'duration_minutes' => 'nullable|integer|min:1',
            'order' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive,draft',
        ]);
        
        $lesson->update($validated);
        
        return redirect()->route('tenant.curriculum.index', $course)
            ->with('success', 'Lesson updated successfully.');
    }

    public function destroyLesson(Course $course, Lesson $lesson)
    {
        $this->authorizeCourse($course);
        $lesson->delete();
        
        return redirect()->route('tenant.curriculum.index', $course)
            ->with('success', 'Lesson deleted successfully.');
    }

    // ==================== CONTENT & NOTES ====================
    
    public function storeContent(Request $request)
    {
        $validated = $request->validate([
            'contentable_type' => 'required|string',
            'contentable_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'nullable|url',
            'video_type' => 'required_with:video_url|in:youtube,vimeo,other',
            'order' => 'nullable|integer|min:0',
        ]);
        
        $validated['user_id'] = Auth::id();
        
        CurriculumContent::create($validated);
        
        return back()->with('success', 'Content added successfully.');
    }

    public function storeNote(Request $request)
    {
        $validated = $request->validate([
            'noteable_type' => 'required|string',
            'noteable_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf|max:10240', // Max 10MB
            'is_downloadable' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        $file = $request->file('file');
        $path = $file->store('curriculum_notes', 'public');

        CurriculumNote::create([
            'tenant_id' => Auth::user()->tenant_id,
            'noteable_type' => $validated['noteable_type'],
            'noteable_id' => $validated['noteable_id'],
            'title' => $validated['title'],
            'file_path' => $path,
            'file_type' => 'pdf',
            'is_downloadable' => $validated['is_downloadable'] ?? false,
            'order' => $validated['order'] ?? 0,
            'user_id' => Auth::id(),
        ]);

        return back()->with('success', 'Note added successfully.');
    }

    public function destroyContent(CurriculumContent $content)
    {
        $content->delete();
        return back()->with('success', 'Content removed successfully.');
    }

    public function destroyNote(Course $course, CurriculumNote $note)
    {
        try {
            // Delete file if it exists
            try {
                if ($note->file_path && Storage::disk('public')->exists($note->file_path)) {
                    Storage::disk('public')->delete($note->file_path);
                }
            } catch (\Exception $e) {
                \Log::warning('Note file delete failed: ' . $e->getMessage());
            }

            $note->delete();
            return back()->with('success', 'Note removed successfully.');
        } catch (\Exception $e) {
            \Log::error('destroyNote failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete note: ' . $e->getMessage());
        }
    }

    // ==================== HELPER ====================
    
    private function authorizeCourse(Course $course)
    {
        if ($course->tenant_id !== Auth::user()->tenant_id) {
            abort(403, 'Unauthorized access to this course.');
        }
    }
}
