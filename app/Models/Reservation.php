<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reservation_number', 'room_id', 'guest_id',
        'check_in_date', 'check_out_date', 'check_in_time', 'check_out_time',
        'guests_count', 'price_per_night', 'total_amount', 'discount', 'paid_amount',
        'status', 'payment_status', 'payment_method',
        'special_requests', 'notes', 'created_by',
    ];

    protected $casts = [
        'check_in_date'  => 'date',
        'check_out_date' => 'date',
        'total_amount'   => 'decimal:2',
        'price_per_night' => 'decimal:2',
        'discount'       => 'decimal:2',
        'paid_amount'    => 'decimal:2',
    ];

    // Relationships
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helpers
    public function getNightsAttribute(): int
    {
        return $this->check_in_date->diffInDays($this->check_out_date);
    }

    public function getBalanceDueAttribute(): float
    {
        return $this->total_amount - $this->paid_amount;
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending'     => 'bg-yellow-100 text-yellow-800',
            'confirmed'   => 'bg-blue-100 text-blue-800',
            'checked_in'  => 'bg-green-100 text-green-800',
            'checked_out' => 'bg-gray-100 text-gray-800',
            'cancelled'   => 'bg-red-100 text-red-800',
            'no_show'     => 'bg-orange-100 text-orange-800',
            default       => 'bg-gray-100 text-gray-800',
        };
    }

    public function getPaymentStatusBadgeAttribute(): string
    {
        return match($this->payment_status) {
            'unpaid'   => 'bg-red-100 text-red-800',
            'partial'  => 'bg-yellow-100 text-yellow-800',
            'paid'     => 'bg-green-100 text-green-800',
            'refunded' => 'bg-purple-100 text-purple-800',
            default    => 'bg-gray-100 text-gray-800',
        };
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['confirmed', 'checked_in']);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('check_in_date', today())
            ->orWhereDate('check_out_date', today());
    }

    // Static helpers
    public static function generateNumber(): string
    {
        do {
            $number = 'RES-' . strtoupper(substr(uniqid(), -6));
        } while (self::where('reservation_number', $number)->exists());
        return $number;
    }

    public static function statuses(): array
    {
        return ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled', 'no_show'];
    }

    public static function paymentMethods(): array
    {
        return ['cash', 'credit_card', 'debit_card', 'bank_transfer', 'online'];
    }
}
