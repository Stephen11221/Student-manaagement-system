<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Timetable extends Model
{
    protected $fillable = [
        'class_id',
        'day_of_week',
        'start_time',
        'end_time',
        'topic',
        'meeting_link',
    ];

    public function class()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function getFormattedStartTimeAttribute(): ?string
    {
        return $this->formatTimeValue($this->start_time);
    }

    public function getFormattedEndTimeAttribute(): ?string
    {
        return $this->formatTimeValue($this->end_time);
    }

    public function getTimeRangeAttribute(): string
    {
        $start = $this->formatted_start_time ?? $this->start_time;
        $end = $this->formatted_end_time ?? $this->end_time;

        return trim($start . ' - ' . $end);
    }

    protected function formatTimeValue(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        foreach (['H:i:s', 'H:i'] as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->format('h:i A');
            } catch (\Throwable $exception) {
                continue;
            }
        }

        return $value;
    }
}
