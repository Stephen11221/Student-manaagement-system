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
        $class = Auth::user()->currentClass;
        $classes = $class ? collect([$class]) : collect();
        $timetables = $class
            ? \App\Models\Timetable::where('class_id', $class->id)->orderBy('day_of_week')->get()
            : collect();
        
        return view('student.timetable.index', compact('classes', 'timetables'));
    }

    public function getHomework()
    {
        $class = Auth::user()->currentClass;
        $homeworks = $class
            ? Homework::where('class_id', $class->id)->orderBy('due_date')->get()
            : collect();
        
        return view('student.homework.index', compact('homeworks'));
    }

    public function submitHomework(Homework $homework)
    {
        abort_unless($homework->class_id === Auth::user()->current_class_id, 403);

        $submission = HomeworkSubmission::where('homework_id', $homework->id)
            ->where('student_id', Auth::id())
            ->first();
        
        return view('student.homework.submit', compact('homework', 'submission'));
    }

    public function storeSubmission(Homework $homework, Request $request)
    {
        abort_unless($homework->class_id === Auth::user()->current_class_id, 403);

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

    public function getAttendance(Request $request)
    {
        $attendanceQuery = Attendance::where('student_id', Auth::id())
            ->with(['classRoom', 'department', 'timetable', 'recordedBy'])
            ->when($request->filled('date'), fn ($query) => $query->whereDate('attendance_date', $request->date))
            ->when($request->filled('class_id'), fn ($query) => $query->where('class_id', $request->class_id))
            ->when($request->filled('department_id'), fn ($query) => $query->where('department_id', $request->department_id))
            ->orderByDesc('attendance_date')
            ->orderByDesc('marked_at');

        $attendance = $attendanceQuery->get();
        $totalClasses = $attendance->count();
        $presentCount = $attendance->where('status', 'present')->count();
        $lateCount = $attendance->where('status', 'late')->count();
        $excusedCount = $attendance->where('status', 'excused')->count();
        $absentCount = $attendance->where('status', 'absent')->count();
        $attendancePercentage = $totalClasses > 0 ? ((($presentCount + $lateCount + $excusedCount) / $totalClasses) * 100) : 0;

        return view('student.attendance.index', compact(
            'attendance',
            'attendancePercentage',
            'presentCount',
            'lateCount',
            'excusedCount',
            'absentCount',
            'totalClasses'
        ));
    }
}
