<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'room_number', 'type', 'description', 'price_per_night',
        'capacity', 'floor', 'status', 'amenities', 'images',
    ];

    protected $casts = [
        'amenities'       => 'array',
        'images'          => 'array',
        'price_per_night' => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function activeReservation()
    {
        return $this->hasOne(Reservation::class)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->latest();
    }

    // ── Accessors ──────────────────────────────────────────────────────────────

    /**
     * Returns all image URLs as an array. Empty array if none.
     */
    public function getImageUrlsAttribute(): array
    {
        if (empty($this->images)) {
            return [];
        }
        return array_map(fn($path) => asset('storage/' . $path), $this->images);
    }

    /**
     * Returns the first image URL, or null. Use this for list/card views.
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image_urls[0] ?? null;
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'available'   => 'bg-green-100 text-green-800',
            'occupied'    => 'bg-red-100 text-red-800',
            'cleaning'    => 'bg-blue-100 text-blue-800',
            'maintenance' => 'bg-yellow-100 text-yellow-800',
            default       => 'bg-gray-100 text-gray-800',
        };
    }

    public function getDisplayStatusAttribute(): string
    {
        if ($this->status === 'maintenance') return 'Maintenance';
        return $this->isCurrentlyOccupied() ? 'Occupied' : 'Available';
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeAvailableForDates($query, $checkIn, $checkOut)
    {
        return $query->where('status', '!=', 'maintenance')
            ->whereDoesntHave('reservations', function ($q) use ($checkIn, $checkOut) {
                $q->whereNotIn('status', ['cancelled', 'no_show'])
                    ->where(function ($q2) use ($checkIn, $checkOut) {
                        $q2->whereBetween('check_in_date', [$checkIn, $checkOut])
                            ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                            ->orWhere(function ($q3) use ($checkIn, $checkOut) {
                                $q3->where('check_in_date', '<=', $checkIn)
                                    ->where('check_out_date', '>=', $checkOut);
                            });
                    });
            });
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    public function isAvailableForDates(string $checkIn, string $checkOut): bool
    {
        if ($this->status === 'maintenance') return false;

        return !$this->reservations()
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->where(function ($q) use ($checkIn, $checkOut) {
                $q->whereBetween('check_in_date', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                    ->orWhere(function ($q2) use ($checkIn, $checkOut) {
                        $q2->where('check_in_date', '<=', $checkIn)
                            ->where('check_out_date', '>=', $checkOut);
                    });
            })->exists();
    }

    public function isCurrentlyOccupied(): bool
    {
        return $this->reservations()
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->where('check_in_date', '<=', today())
            ->where('check_out_date', '>', today())
            ->exists();
    }

    public static function types(): array
    {
        return ['single', 'double', 'twin', 'suite', 'deluxe', 'presidential'];
    }

    public static function statuses(): array
    {
        return ['available', 'maintenance'];
    }

    public static function amenitiesList(): array
    {
        return ['WiFi', 'TV', 'Air Conditioning', 'Mini Bar', 'Safe', 'Balcony', 'Bathtub', 'Jacuzzi', 'Sea View', 'City View', 'Kitchen', 'Parking'];
    }
}
