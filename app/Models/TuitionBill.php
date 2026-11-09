<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TuitionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TuitionBill extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'bill_title',
        'month',
        'year',
        'amount',
        'status',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'integer',
            'year' => 'integer',
            'amount' => 'decimal:2',
            'status' => TuitionStatus::class,
            'due_date' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(TuitionPayment::class);
    }

    public function payment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(TuitionPayment::class)->latestOfMany();
    }

    public function paymentTransactions(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(PaymentTransaction::class, 'payable');
    }

    public function latestPaymentTransaction(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(PaymentTransaction::class, 'payable')->latestOfMany();
    }
}
