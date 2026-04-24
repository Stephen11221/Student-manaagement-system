<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffMeeting extends Model
{
    protected $fillable = [
        'team_role',
        'audience_type',
        'staff_id',
        'meeting_type',
        'title',
        'scheduled_at',
        'location',
        'meeting_link',
        'notes',
        'status',
        'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
