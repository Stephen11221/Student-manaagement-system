<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Department;
use App\Models\User;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function __construct(private readonly AttendanceService $attendanceService)
    {
    }

    public function dashboard(Request $request)
    {
        $filters = $this->pullFilters($request);
        $summary = $this->attendanceService->dailySummary($filters);
        $records = $this->attendanceService->query($filters)->paginate(20)->withQueryString();
        $classes = ClassRoom::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $calendarMonth = Carbon::parse($filters['date'] ?? now()->toDateString())->startOfMonth();
        $calendar = $this->buildCalendar($calendarMonth, $filters);

        return view('attendance.dashboard', compact('summary', 'records', 'classes', 'departments', 'filters', 'calendarMonth', 'calendar'));
    }

    public function manageClass(ClassRoom $class, Request $request)
    {
        $filters = $this->pullFilters($request, ['class_id' => $class->id]);
        $summary = $this->attendanceService->dailySummary($filters);
        $records = $this->attendanceService->query($filters)->paginate(20)->withQueryString();

        return view('attendance.manage', [
            'scopeType' => 'class',
            'scope' => $class,
            'summary' => $summary,
            'records' => $records,
            'filters' => $filters,
            'students' => $class->students()->orderBy('name')->get(),
        ]);
    }

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'scope_type' => ['required', 'in:class,department'],
            'scope_id' => ['required', 'integer'],
            'attendance_date' => ['required', 'date'],
            'attendance' => ['required', 'array'],
            'attendance.*.user_id' => ['required', 'exists:users,id'],
            'attendance.*.status' => ['required', 'in:present,absent,late,excused'],
            'attendance.*.remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $scope = $this->resolveScope($validated['scope_type'], (int) $validated['scope_id']);
        $rows = collect($validated['attendance'])->map(fn ($row) => [
            'user' => User::find($row['user_id']),
            'status' => $row['status'],
            'remarks' => $row['remarks'] ?? null,
        ])->filter(fn ($row) => $row['user']);

        $this->attendanceService->bulkMark(
            $scope,
            Carbon::parse($validated['attendance_date']),
            $rows,
            Auth::user(),
            ['source' => 'bulk']
        );

        return back()->with('status', 'Attendance saved successfully.');
    }

    public function checkIn(Request $request)
    {
        $validated = $request->validate([
            'scope_type' => ['required', 'in:class,department,global'],
            'scope_id' => ['nullable', 'integer'],
        ]);

        $scope = $this->resolveScope($validated['scope_type'], (int) ($validated['scope_id'] ?? 0));
        $attendance = $this->attendanceService->checkIn(Auth::user(), $scope, now(), ['actor' => Auth::user()]);

        return $this->respond($request, $attendance, 'Checked in successfully.');
    }

    public function checkOut(Request $request)
    {
        $validated = $request->validate([
            'scope_type' => ['required', 'in:class,department,global'],
            'scope_id' => ['nullable', 'integer'],
        ]);

        $scope = $this->resolveScope($validated['scope_type'], (int) ($validated['scope_id'] ?? 0));
        $attendance = $this->attendanceService->checkOut(Auth::user(), $scope, now(), ['actor' => Auth::user()]);

        return $this->respond($request, $attendance, 'Checked out successfully.');
    }

    public function csv(Request $request)
    {
        $filters = $this->pullFilters($request);
        $records = $this->attendanceService->query($filters)->get();
        $csv = $this->attendanceService->exportCsv($records);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=attendance-report.csv',
        ]);
    }

    public function report(Request $request)
    {
        $filters = $this->pullFilters($request);
        $summary = $this->attendanceService->dailySummary($filters);
        $records = $this->attendanceService->query($filters)->get();

        return view('attendance.report', compact('filters', 'summary', 'records'));
    }

    public function apiSummary(Request $request)
    {
        $filters = $this->pullFilters($request);

        return response()->json($this->attendanceService->dailySummary($filters));
    }

    public function apiRecords(Request $request)
    {
        $filters = $this->pullFilters($request);

        return response()->json([
            'data' => $this->attendanceService->query($filters)->paginate(25),
        ]);
    }

    protected function pullFilters(Request $request, array $defaults = []): array
    {
        return array_filter([
            'date' => $request->query('date', $defaults['date'] ?? null),
            'from' => $request->query('from'),
            'to' => $request->query('to'),
            'class_id' => $request->query('class_id', $defaults['class_id'] ?? null),
            'department_id' => $request->query('department_id'),
            'status' => $request->query('status'),
            'student_id' => $request->query('student_id'),
        ], fn ($value) => $value !== null && $value !== '');
    }

    protected function resolveScope(string $scopeType, int $scopeId): array
    {
        return match ($scopeType) {
            'class' => (function () use ($scopeId) {
                $class = ClassRoom::findOrFail($scopeId);

                return [
                    'scope_type' => 'class',
                    'scope_id' => $class->id,
                    'class_id' => $class->id,
                    'department_id' => null,
                ];
            })(),
            'department' => (function () use ($scopeId) {
                $department = Department::findOrFail($scopeId);

                return [
                    'scope_type' => 'department',
                    'scope_id' => $department->id,
                    'class_id' => null,
                    'department_id' => $department->id,
                ];
            })(),
            default => [
                'scope_type' => 'global',
                'scope_id' => 0,
                'class_id' => null,
                'department_id' => null,
            ],
        };
    }

    protected function respond(Request $request, Attendance $attendance, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'attendance' => $attendance,
            ]);
        }

        return back()->with('status', $message);
    }

    protected function buildCalendar(Carbon $month, array $filters): array
    {
        $daysInMonth = $month->daysInMonth;
        $records = $this->attendanceService->query([
            ...$filters,
            'from' => $month->copy()->startOfMonth()->toDateString(),
            'to' => $month->copy()->endOfMonth()->toDateString(),
        ])->get()->groupBy(fn ($record) => optional($record->attendance_date)->toDateString() ?? (string) $record->attendance_date);

        $calendar = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = $month->copy()->day($day)->toDateString();
            $calendar[] = [
                'date' => $date,
                'label' => Carbon::parse($date)->format('D'),
                'count' => $records->get($date, collect())->count(),
                'present' => $records->get($date, collect())->where('status', 'present')->count(),
                'late' => $records->get($date, collect())->where('status', 'late')->count(),
                'absent' => $records->get($date, collect())->where('status', 'absent')->count(),
                'excused' => $records->get($date, collect())->where('status', 'excused')->count(),
            ];
        }

        return $calendar;
    }
}
