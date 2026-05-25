<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone',
        'id_number', 'nationality', 'date_of_birth',
        'address', 'city', 'country', 'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    // Relationships
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

    // Helpers
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getTotalStaysAttribute(): int
    {
        return $this->reservations()->whereIn('status', ['checked_out'])->count();
    }

    public function getTotalSpentAttribute(): float
    {
        return $this->reservations()
            ->whereIn('status', ['checked_in', 'checked_out'])
            ->sum('total_amount');
    }
}
