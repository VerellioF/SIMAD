<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TuitionPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tuition_bill_id',
        'verified_by',
        'receipt_number',
        'amount_paid',
        'payment_method',
        'bank_sender_name',
        'proof_attachment',
        'payment_date',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'amount_paid' => 'decimal:2',
            'payment_method' => PaymentMethod::class,
            'payment_date' => 'date',
            'verified_at' => 'datetime',
        ];
    }

    public function tuitionBill(): BelongsTo
    {
        return $this->belongsTo(TuitionBill::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
