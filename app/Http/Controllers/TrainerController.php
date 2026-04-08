<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\Timetable;
use App\Models\Attendance;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainerController extends Controller
{
    // Classes
    public function getClasses()
    {
        $classes = Auth::user()->taughtClasses()->get();
        return view('trainer.classes.index', compact('classes'));
    }

    public function createClass()
    {
        return view('trainer.classes.create');
    }

    public function storeClass(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $classroom = ClassRoom::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'trainer_id' => Auth::id(),
            'status' => 'active',
        ]);

        return redirect()->route('trainer.classes.index')->with('success', 'Class created successfully!');
    }

    public function getTimetable(ClassRoom $class)
    {
        $timetables = $class->timetables()->get();
        return view('trainer.timetable.index', compact('class', 'timetables'));
    }

    public function getHomework(ClassRoom $class)
    {
        $homeworks = $class->homeworks()->get();
        return view('trainer.homework.index', compact('class', 'homeworks'));
    }

    public function viewSubmissions(Homework $homework)
    {
        $submissions = $homework->submissions()->with('student')->get();
        return view('trainer.homework.submissions', compact('homework', 'submissions'));
    }

    public function gradeSubmission(HomeworkSubmission $submission, Request $request)
    {
        $validated = $request->validate([
            'marks' => ['required', 'integer', 'min:0', 'max:100'],
            'feedback' => ['nullable', 'string'],
        ]);

        $submission->update([
            ...$validated,
            'status' => 'graded',
        ]);

        Notification::create([
            'user_id' => $submission->student_id,
            'title' => 'Homework Graded',
            'message' => 'Your homework has been graded!',
            'type' => 'success',
        ]);

        return redirect()->back()->with('success', 'Submission graded!');
    }
}
