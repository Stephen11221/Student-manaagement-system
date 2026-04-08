<?php

namespace App\Http\Controllers;

use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function getTimetable()
    {
        $classes = Auth::user()->enrolledClasses()->get();
        $timetables = $classes->count() > 0 
            ? \App\Models\Timetable::whereIn('class_id', $classes->pluck('id'))->orderBy('day_of_week')->get()
            : collect();
        
        return view('student.timetable.index', compact('classes', 'timetables'));
    }

    public function getHomework()
    {
        $classes = Auth::user()->enrolledClasses()->get();
        $homeworks = $classes->count() > 0
            ? Homework::whereIn('class_id', $classes->pluck('id'))->orderBy('due_date')->get()
            : collect();
        
        return view('student.homework.index', compact('homeworks'));
    }

    public function submitHomework(Homework $homework)
    {
        $submission = HomeworkSubmission::where('homework_id', $homework->id)
            ->where('student_id', Auth::id())
            ->first();
        
        return view('student.homework.submit', compact('homework', 'submission'));
    }

    public function storeSubmission(Homework $homework, Request $request)
    {
        $rules = ['homework_id' => ['required']];
        
        if ($homework->submission_type === 'written') {
            $rules['content'] = ['required', 'string'];
        } else {
            $rules['file'] = ['required', 'file', 'max:10240'];
        }

        $validated = $request->validate($rules);

        $submission = HomeworkSubmission::firstOrNew([
            'homework_id' => $homework->id,
            'student_id' => Auth::id(),
        ]);

        if ($homework->submission_type === 'written') {
            $submission->content = $validated['content'];
        } else {
            if ($request->hasFile('file')) {
                $path = $request->file('file')->store('homework', 'public');
                $submission->file_path = $path;
            }
        }

        $submission->status = 'submitted';
        $submission->submitted_at = now();
        $submission->save();

        return redirect()->route('student.homework.index')->with('success', 'Homework submitted!');
    }

    public function getAttendance()
    {
        $attendance = Attendance::where('student_id', Auth::id())
            ->with('classRoom', 'timetable')
            ->get();

        $totalClasses = $attendance->count();
        $presentCount = $attendance->where('status', 'present')->count();
        $attendancePercentage = $totalClasses > 0 ? ($presentCount / $totalClasses * 100) : 0;

        return view('student.attendance.index', compact('attendance', 'attendancePercentage', 'presentCount', 'totalClasses'));
    }
}
