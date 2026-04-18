<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Department;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    public function scopeData(?ClassRoom $class = null, ?Department $department = null): array
    {
        if ($class) {
            return [
                'scope_type' => 'class',
                'scope_id' => $class->id,
                'class_id' => $class->id,
                'department_id' => null,
            ];
        }

        if ($department) {
            return [
                'scope_type' => 'department',
                'scope_id' => $department->id,
                'class_id' => null,
                'department_id' => $department->id,
            ];
        }

        return [
            'scope_type' => 'global',
            'scope_id' => 0,
            'class_id' => null,
            'department_id' => null,
        ];
    }

    public function query(array $filters = []): Builder
    {
        $query = Attendance::query()
            ->with(['student', 'classRoom', 'department', 'recordedBy', 'timetable'])
            ->orderByDesc('attendance_date')
            ->orderByDesc('marked_at');

        if (!empty($filters['date'])) {
            $query->whereDate('attendance_date', Carbon::parse($filters['date'])->toDateString());
        }

        if (!empty($filters['from'])) {
            $query->whereDate('attendance_date', '>=', Carbon::parse($filters['from'])->toDateString());
        }

        if (!empty($filters['to'])) {
            $query->whereDate('attendance_date', '<=', Carbon::parse($filters['to'])->toDateString());
        }

        if (!empty($filters['class_id'])) {
            $query->where('class_id', $filters['class_id']);
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        return $query;
    }

    public function dailySummary(array $filters = []): array
    {
        $query = $this->query($filters);
        $records = (clone $query)->get();

        $total = $records->count();
        $present = $records->where('status', 'present')->count();
        $late = $records->where('status', 'late')->count();
        $absent = $records->where('status', 'absent')->count();
        $excused = $records->where('status', 'excused')->count();
        $attendanceRate = $total > 0 ? round((($present + $late + $excused) / $total) * 100, 2) : 0;

        return [
            'records' => $records,
            'total' => $total,
            'present' => $present,
            'late' => $late,
            'absent' => $absent,
            'excused' => $excused,
            'attendanceRate' => $attendanceRate,
            'absentees' => $records->where('status', 'absent')->pluck('student')->filter()->unique('id')->values(),
            'dates' => $records->pluck('attendance_date')->unique()->sort()->values(),
        ];
    }

    public function record(
        User $student,
        array $scope,
        Carbon|string $date,
        string $status,
        array $extra = [],
        ?User $actor = null
    ): Attendance {
        $date = $date instanceof Carbon ? $date->toDateString() : Carbon::parse($date)->toDateString();

        return DB::transaction(function () use ($student, $scope, $date, $status, $extra, $actor) {
            $attendance = Attendance::updateOrCreate(
                [
                    'attendance_date' => $date,
                    'scope_type' => $scope['scope_type'],
                    'scope_id' => $scope['scope_id'],
                    'student_id' => $student->id,
                ],
                [
                    ...$scope,
                    'student_id' => $student->id,
                    'attendance_date' => $date,
                    'status' => $status,
                    'marked_at' => now(),
                    'recorded_by' => $actor?->id,
                    'source' => $extra['source'] ?? 'manual',
                    'remarks' => $extra['remarks'] ?? null,
                    'timetable_id' => $extra['timetable_id'] ?? null,
                    'check_in_at' => $extra['check_in_at'] ?? null,
                    'check_out_at' => $extra['check_out_at'] ?? null,
                ]
            );

            return $attendance->fresh(['student', 'classRoom', 'department', 'recordedBy', 'timetable']);
        });
    }

    public function checkIn(User $user, array $scope, ?Carbon $at = null, array $extra = []): Attendance
    {
        $at = $at ?? now();

        return $this->record($user, $scope, $at, 'present', [
            ...$extra,
            'check_in_at' => $at,
            'source' => $extra['source'] ?? 'check_in',
        ], $extra['actor'] ?? null);
    }

    public function checkOut(User $user, array $scope, ?Carbon $at = null, array $extra = []): Attendance
    {
        $at = $at ?? now();
        $attendance = Attendance::where([
            'attendance_date' => $at->toDateString(),
            'scope_type' => $scope['scope_type'],
            'scope_id' => $scope['scope_id'],
            'student_id' => $user->id,
        ])->first();

        if (! $attendance) {
            $attendance = $this->record($user, $scope, $at, 'present', [
                ...$extra,
                'check_out_at' => $at,
                'source' => $extra['source'] ?? 'check_out',
            ], $extra['actor'] ?? null);
        } else {
            $attendance->check_out_at = $at;
            $attendance->recorded_by = $extra['actor']?->id ?? $attendance->recorded_by;
            if (empty($attendance->status)) {
                $attendance->status = 'present';
            }
            $attendance->source = $extra['source'] ?? 'check_out';
            $attendance->save();
            $attendance->load(['student', 'classRoom', 'department', 'recordedBy', 'timetable']);
        }

        return $attendance;
    }

    public function bulkMark(array $scope, Carbon|string $date, iterable $rows, ?User $actor = null, array $extra = []): Collection
    {
        $saved = collect();

        foreach ($rows as $row) {
            if (empty($row['user']) || empty($row['status'])) {
                continue;
            }

            $saved->push($this->record(
                $row['user'],
                $scope,
                $date,
                $row['status'],
                [
                    'remarks' => $row['remarks'] ?? ($extra['remarks'] ?? null),
                    'timetable_id' => $row['timetable_id'] ?? ($extra['timetable_id'] ?? null),
                    'check_in_at' => $row['check_in_at'] ?? null,
                    'check_out_at' => $row['check_out_at'] ?? null,
                    'source' => $extra['source'] ?? 'bulk',
                ],
                $actor
            ));
        }

        return $saved;
    }

    public function exportCsv(Collection $records): string
    {
        $handle = fopen('php://temp', 'w+');
        fputcsv($handle, [
            'Date',
            'Scope',
            'Name',
            'Email',
            'Status',
            'Check In',
            'Check Out',
            'Recorded By',
            'Remarks',
        ]);

        foreach ($records as $record) {
            fputcsv($handle, [
                optional($record->attendance_date)->format('Y-m-d') ?? $record->attendance_date,
                $record->scope_type === 'class' ? ($record->classRoom?->name ?? 'Class') : ($record->department?->name ?? 'Department'),
                $record->student?->name ?? '-',
                $record->student?->email ?? '-',
                ucfirst($record->status),
                $record->check_in_at?->format('H:i:s') ?? '-',
                $record->check_out_at?->format('H:i:s') ?? '-',
                $record->recordedBy?->name ?? '-',
                $record->remarks ?? '-',
            ]);
        }

        rewind($handle);

        return stream_get_contents($handle) ?: '';
    }
}
