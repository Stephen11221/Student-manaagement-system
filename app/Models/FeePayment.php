<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeePayment extends Model
{
    protected $fillable = [
        'student_id',
        'academic_year',
        'term',
        'amount_due',
        'amount_paid',
        'payment_method',
        'phone_number',
        'receipt_number',
        'paid_at',
        'status',
        'notes',
        'checkout_request_id',
        'merchant_request_id',
        'transaction_id',
        'daraja_response_code',
        'daraja_response_description',
        'daraja_payload',
        'daraja_requested_at',
        'daraja_completed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount_due' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'paid_at' => 'datetime',
            'daraja_payload' => 'array',
            'daraja_requested_at' => 'datetime',
            'daraja_completed_at' => 'datetime',
        ];
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
