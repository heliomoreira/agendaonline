<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Agenda extends Model
{
    use HasFactory;

    protected $table = 'agenda';

    protected $fillable = [
        // Dados básicos
        'client_id',
        'professional_id',
        'service_id',
        'day',
        'start_hour',
        'end_hour',
        'notes',

        // Pagamento Stripe
        'payment_intent_id',
        'payment_method',
        'paid',
        'amount',
        'status',

        // Dados do cliente (bookings sem login)
        'client_name',
        'client_email',
        'client_phone_1',
        'client_phone_2',

        // Metadata
        'confirmed_at',
        'cancelled_at',
        'cancellation_reason',
        'cancelled_by',
    ];

    protected $casts = [
        'day' => 'date',
        'paid' => 'boolean',
        'amount' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PAYMENT_PENDING = 'payment_pending';
    const STATUS_PAYMENT_FAILED = 'payment_failed';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_NO_SHOW = 'no_show';

    /**
     * Payment methods
     */
    const PAYMENT_CARD = 'card';
    const PAYMENT_MULTIBANCO = 'multibanco';
    const PAYMENT_MBWAY = 'mbway';

    /* ═══════════════════════════════════════════════
       RELATIONSHIPS
    ═══════════════════════════════════════════════ */

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class, 'professional_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /* ═══════════════════════════════════════════════
       SCOPES
    ═══════════════════════════════════════════════ */

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopePaymentPending($query)
    {
        return $query->where('status', self::STATUS_PAYMENT_PENDING);
    }

    public function scopePaid($query)
    {
        return $query->where('paid', true);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('paid', false);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('day', '>=', now()->toDateString())
            ->whereIn('status', [self::STATUS_CONFIRMED, self::STATUS_PENDING])
            ->orderBy('day')
            ->orderBy('start_hour');
    }

    public function scopePast($query)
    {
        return $query->where('day', '<', now()->toDateString())
            ->orderBy('day', 'desc')
            ->orderBy('start_hour', 'desc');
    }

    public function scopeToday($query)
    {
        return $query->where('day', now()->toDateString())
            ->orderBy('start_hour');
    }

    public function scopeByProfessional($query, $professionalId)
    {
        return $query->where('professional_id', $professionalId);
    }

    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /* ═══════════════════════════════════════════════
       ACCESSORS & MUTATORS
    ═══════════════════════════════════════════════ */

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->day->locale('pt')->isoFormat('dddd, D [de] MMMM [de] YYYY');
    }

    /**
     * Get formatted time range
     */
    public function getTimeRangeAttribute(): string
    {
        return "{$this->start_hour} - {$this->end_hour}";
    }

    /**
     * Get client display name (from relationship or direct field)
     */
    public function getClientDisplayNameAttribute(): string
    {
        return $this->client?->name ?? $this->client_name ?? 'Cliente';
    }

    /**
     * Get client display email
     */
    public function getClientDisplayEmailAttribute(): ?string
    {
        return $this->client?->email ?? $this->client_email;
    }

    /**
     * Get client display phone
     */
    public function getClientDisplayPhoneAttribute(): ?string
    {
        return $this->client?->phone_1 ?? $this->client_phone_1;
    }

    /**
     * Check if booking is confirmed
     */
    public function getIsConfirmedAttribute(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    /**
     * Check if booking is cancelled
     */
    public function getIsCancelledAttribute(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Check if booking is in the past
     */
    public function getIsPastAttribute(): bool
    {
        return $this->day->isPast();
    }

    /**
     * Check if booking requires payment
     */
    public function getRequiresPaymentAttribute(): bool
    {
        return $this->amount > 0 && !$this->paid;
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_CONFIRMED => 'success',
            self::STATUS_PENDING => 'warning',
            self::STATUS_PAYMENT_PENDING => 'info',
            self::STATUS_PAYMENT_FAILED => 'danger',
            self::STATUS_COMPLETED => 'primary',
            self::STATUS_CANCELLED => 'secondary',
            self::STATUS_NO_SHOW => 'dark',
            default => 'secondary',
        };
    }

    /**
     * Get status label (PT)
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_CONFIRMED => 'Confirmada',
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_PAYMENT_PENDING => 'Aguardando Pagamento',
            self::STATUS_PAYMENT_FAILED => 'Pagamento Falhou',
            self::STATUS_COMPLETED => 'Completada',
            self::STATUS_CANCELLED => 'Cancelada',
            self::STATUS_NO_SHOW => 'Não Compareceu',
            default => 'Desconhecido',
        };
    }

    /**
     * Get payment method label
     */
    public function getPaymentMethodLabelAttribute(): ?string
    {
        if (!$this->payment_method) return null;

        return match($this->payment_method) {
            self::PAYMENT_CARD => 'Cartão',
            self::PAYMENT_MULTIBANCO => 'Multibanco',
            self::PAYMENT_MBWAY => 'MB WAY',
            default => ucfirst($this->payment_method),
        };
    }

    /* ═══════════════════════════════════════════════
       METHODS
    ═══════════════════════════════════════════════ */

    /**
     * Confirm booking
     */
    public function confirm(): self
    {
        $this->update([
            'status' => self::STATUS_CONFIRMED,
            'confirmed_at' => now(),
        ]);

        return $this;
    }

    /**
     * Cancel booking
     */
    public function cancel(string $reason = null, string $cancelledBy = 'client'): self
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
            'cancelled_by' => $cancelledBy,
        ]);

        return $this;
    }

    /**
     * Mark as completed
     */
    public function complete(): self
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
        ]);

        return $this;
    }

    /**
     * Mark as no show
     */
    public function markAsNoShow(): self
    {
        $this->update([
            'status' => self::STATUS_NO_SHOW,
        ]);

        return $this;
    }

    /**
     * Mark as paid
     */
    public function markAsPaid(string $paymentIntentId = null, string $paymentMethod = null): self
    {
        $updates = [
            'paid' => true,
            'status' => self::STATUS_CONFIRMED,
            'confirmed_at' => now(),
        ];

        if ($paymentIntentId) {
            $updates['payment_intent_id'] = $paymentIntentId;
        }

        if ($paymentMethod) {
            $updates['payment_method'] = $paymentMethod;
        }

        $this->update($updates);

        return $this;
    }

    /**
     * Check if booking can be cancelled
     */
    public function canBeCancelled(): bool
    {
        // Não pode cancelar se já está cancelada, completada, ou é no-show
        if (in_array($this->status, [self::STATUS_CANCELLED, self::STATUS_COMPLETED, self::STATUS_NO_SHOW])) {
            return false;
        }

        // Não pode cancelar se já passou
        if ($this->is_past) {
            return false;
        }

        return true;
    }

    /**
     * Check if booking can be rescheduled
     */
    public function canBeRescheduled(): bool
    {
        return $this->canBeCancelled();
    }

    public function scopeVisibleTo($query, $user)
    {
        if ($user && $user->isRestrictedToOwnAgenda()) {
            return $query->where('professional_id', $user->professional_id);
        }

        return $query;
    }

    public function paymentStatus()
    {
        return $this->belongsTo(PaymentStatus::class);
    }

    protected static function booted()
    {
        static::creating(function ($agenda) {
            if (is_null($agenda->payment_status_id)) {
                $agenda->payment_status_id = PaymentStatus::default()?->id;
            }
        });
    }
}
