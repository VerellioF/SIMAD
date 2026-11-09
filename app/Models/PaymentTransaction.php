<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'payable_type',
        'payable_id',
        'user_id',
        'payer_name',
        'payer_email',
        'payer_phone',
        'amount',
        'fee_amount',
        'total_amount',
        'payment_method',
        'payment_provider',
        'provider_reference',
        'qr_string',
        'qr_url',
        'checkout_url',
        'status',
        'paid_at',
        'expired_at',
        'raw_payload',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
        'raw_payload' => 'array',
    ];

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function formattedAmount(): string
    {
        return 'Rp ' . number_format((float) $this->amount, 0, ',', '.');
    }

    public function formattedFee(): string
    {
        return 'Rp ' . number_format((float) $this->fee_amount, 0, ',', '.');
    }

    public function formattedTotal(): string
    {
        return 'Rp ' . number_format((float) $this->total_amount, 0, ',', '.');
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            'paid' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
            'expired' => 'bg-slate-100 text-slate-700 border-slate-200',
            default => 'bg-red-100 text-red-800 border-red-200',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'paid' => 'Lunas (Terverifikasi)',
            'pending' => 'Menunggu Pembayaran',
            'expired' => 'Kadaluarsa',
            default => 'Gagal',
        };
    }
}
