<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PpdbStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PpdbRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_number',
        'child_name',
        'nik',
        'gender',
        'birth_place',
        'birth_date',
        'previous_school',
        'parent_name',
        'parent_whatsapp',
        'parent_job',
        'address',
        'file_birth_certificate',
        'file_family_card',
        'file_photo',
        'registration_fee',
        'payment_status',
        'payment_method',
        'file_payment_proof',
        'paid_at',
        'status',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'status' => PpdbStatus::class,
            'registration_fee' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function formattedFee(): string
    {
        return 'Rp ' . number_format($this->registration_fee, 0, ',', '.');
    }

    public function paymentStatusLabel(): string
    {
        return match ($this->payment_status) {
            'paid' => 'Lunas (Terverifikasi)',
            default => 'Menunggu Pembayaran',
        };
    }

    public function paymentStatusBadge(): string
    {
        return match ($this->payment_status) {
            'paid' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            default => 'bg-amber-100 text-amber-800 border-amber-200',
        };
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
