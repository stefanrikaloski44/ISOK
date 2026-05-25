<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Guest;
use App\Models\Reservation;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function home()
    {
        $rooms = Room::where('status', '!=', 'maintenance')->take(6)->get();
        return view('public.home', compact('rooms'));
    }

    public function rooms(Request $request)
    {
        $query = Room::where('status', '!=', 'maintenance');

        // Type filter
        if ($request->type) {
            $query->where('type', $request->type);
        }

        // Price filters
        if ($request->min_price) {
            $query->where('price_per_night', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('price_per_night', '<=', $request->max_price);
        }

        // Date availability filter — only applied when both dates are present AND different
        $checkIn = $request->check_in ?? today()->toDateString();
        $checkOut = $request->check_out ?? today()->toDateString();

        if ($checkIn && $checkOut && $checkIn !== $checkOut) {
            $query->availableForDates($checkIn, $checkOut);
        }

        $rooms = $query->orderBy('price_per_night')->paginate(12)->withQueryString();

        return view('public.rooms', compact('rooms'));
    }

    public function room(Room $room, Request $request)
    {
        // Pass dates through so the booking card can pre-fill them
        $checkIn = $request->check_in;
        $checkOut = $request->check_out;

        return view('public.room', compact('room', 'checkIn', 'checkOut'));
    }

    public function bookingForm(Room $room, Request $request)
    {
        return view('public.book', compact('room'));
    }

    public function bookingStore(Request $request, Room $room)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:30',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'guests_count' => 'required|integer|min:1',
            'special_requests' => 'nullable|string',
        ]);

        // Find or create guest
        $guest = Guest::firstOrCreate(
            ['email' => $data['email']],
            [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone' => $data['phone'] ?? null,
            ]
        );

        // Check availability by date overlap
        $conflict = Reservation::where('room_id', $room->id)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->where(function ($q) use ($data) {
                $q->whereBetween('check_in_date', [$data['check_in_date'], $data['check_out_date']])
                    ->orWhereBetween('check_out_date', [$data['check_in_date'], $data['check_out_date']])
                    ->orWhere(function ($q2) use ($data) {
                        $q2->where('check_in_date', '<=', $data['check_in_date'])
                            ->where('check_out_date', '>=', $data['check_out_date']);
                    });
            })->exists();

        if ($conflict) {
            return back()->withInput()->with('error', 'This room is not available for the selected dates. Please choose different dates.');
        }

        $nights = \Carbon\Carbon::parse($data['check_in_date'])->diffInDays($data['check_out_date']);
        $total = $room->price_per_night * $nights;

        $reservation = Reservation::create([
            'reservation_number' => Reservation::generateNumber(),
            'room_id' => $room->id,
            'guest_id' => $guest->id,
            'check_in_date' => $data['check_in_date'],
            'check_out_date' => $data['check_out_date'],
            'guests_count' => $data['guests_count'],
            'price_per_night' => $room->price_per_night,
            'total_amount' => $total,
            'discount' => 0,
            'paid_amount' => 0,
            'status' => 'confirmed',
            'payment_status' => 'unpaid',
            'special_requests' => $data['special_requests'] ?? null,
        ]);

        return redirect()->route('public.confirmation', $reservation)
            ->with('success', 'Reservation confirmed!');
    }

    public function confirmation(Reservation $reservation)
    {
        $reservation->load(['room', 'guest']);
        return view('public.confirmation', compact('reservation'));
    }

    public function myReservations()
    {
        $user = auth()->user();
        $guest = Guest::where('email', $user->email)->first();
        $reservations = $guest
            ? $guest->reservations()->with('room')->orderBy('check_in_date', 'desc')->paginate(10)
            : collect();
        return view('public.my-reservations', compact('reservations'));
    }
}
