<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\Guest;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::with(['room', 'guest'])->latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('reservation_number', 'like', "%{$request->search}%")
                    ->orWhereHas('guest', function ($q2) use ($request) {
                        $q2->where('first_name', 'like', "%{$request->search}%")
                            ->orWhere('last_name',  'like', "%{$request->search}%")
                            ->orWhere('email',      'like', "%{$request->search}%");
                    });
            });
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->date_from) {
            $query->where('check_in_date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->where('check_in_date', '<=', $request->date_to);
        }

        $reservations = $query->paginate(20)->withQueryString();

        return view('reservations.index', compact('reservations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'room_id'          => 'required|exists:rooms,id',
            'guest_id'         => 'required|exists:guests,id',
            'check_in_date'    => 'required|date|after_or_equal:today',
            'check_out_date'   => 'required|date|after:check_in_date',
            'guests_count'     => 'required|integer|min:1',
            'discount'         => 'nullable|numeric|min:0',
            'payment_method'   => 'nullable|string',
            'special_requests' => 'nullable|string',
            'notes'            => 'nullable|string',
        ]);

        $room   = Room::findOrFail($data['room_id']);
        $nights = \Carbon\Carbon::parse($data['check_in_date'])
            ->diffInDays(\Carbon\Carbon::parse($data['check_out_date']));

        // Check availability
        $conflict = Reservation::where('room_id', $data['room_id'])
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
            return back()->withInput()
                ->with('error', 'This room is not available for the selected dates.');
        }

        $pricePerNight = $room->price_per_night;
        $subtotal      = $pricePerNight * $nights;
        $discount      = $data['discount'] ?? 0;
        $total         = max(0, $subtotal - $discount);

        $reservation = Reservation::create(array_merge($data, [
            'reservation_number' => Reservation::generateNumber(),
            'price_per_night'    => $pricePerNight,
            'total_amount'       => $total,
            'discount'           => $discount,
            'paid_amount'        => 0,
            'status'             => 'confirmed',
            'payment_status'     => 'unpaid',
            'created_by'         => auth()->id(),
        ]));

        return redirect()->route('admin.reservations.show', $reservation)
            ->with('success', "Reservation {$reservation->reservation_number} created!");
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['room', 'guest', 'payments.receivedBy']);
        return view('reservations.show', compact('reservation'));
    }

    public function edit(Reservation $reservation)
    {
        if (in_array($reservation->status, ['checked_out', 'cancelled'])) {
            return back()->with('error', 'Cannot edit a closed reservation.');
        }
        $rooms  = Room::where('status', '!=', 'maintenance')->orderBy('room_number')->get();
        $guests = Guest::orderBy('last_name')->get();
        return view('reservations.edit', compact('reservation', 'rooms', 'guests'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        $data = $request->validate([
            'room_id'          => 'required|exists:rooms,id',
            'guest_id'         => 'required|exists:guests,id',
            'check_in_date'    => 'required|date',
            'check_out_date'   => 'required|date|after:check_in_date',
            'guests_count'     => 'required|integer|min:1',
            'discount'         => 'nullable|numeric|min:0',
            'payment_method'   => 'nullable|string',
            'special_requests' => 'nullable|string',
            'notes'            => 'nullable|string',
            'status'           => 'required|in:' . implode(',', Reservation::statuses()),
        ]);

        $room   = Room::findOrFail($data['room_id']);
        $nights = \Carbon\Carbon::parse($data['check_in_date'])
            ->diffInDays(\Carbon\Carbon::parse($data['check_out_date']));

        // Check availability (exclude self)
        $conflict = Reservation::where('room_id', $data['room_id'])
            ->where('id', '!=', $reservation->id)
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
            return back()->withInput()
                ->with('error', 'This room is not available for the selected dates.');
        }

        $discount = $data['discount'] ?? 0;
        $total    = max(0, ($room->price_per_night * $nights) - $discount);

        $reservation->update(array_merge($data, [
            'price_per_night' => $room->price_per_night,
            'total_amount'    => $total,
            'discount'        => $discount,
        ]));

        return redirect()->route('admin.reservations.show', $reservation)
            ->with('success', 'Reservation updated successfully!');
    }

    public function destroy(Reservation $reservation)
    {
        if (in_array($reservation->status, ['checked_in'])) {
            return back()->with('error', 'Cannot delete an active check-in.');
        }
        $reservation->delete();
        return redirect()->route('admin.reservations.index')
            ->with('success', 'Reservation deleted successfully!');
    }

    // Check In
    public function checkIn(Reservation $reservation)
    {
        if ($reservation->status !== 'confirmed') {
            return back()->with('error', 'Only confirmed reservations can be checked in.');
        }

        DB::transaction(function () use ($reservation) {
            $reservation->update([
                'status'        => 'checked_in',
                'check_in_time' => now()->format('H:i:s'),
            ]);
            $reservation->room->update(['status' => 'occupied']);
        });

        return back()->with('success', 'Guest checked in successfully!');
    }

    // Check Out
    public function checkOut(Reservation $reservation)
    {
        if ($reservation->status !== 'checked_in') {
            return back()->with('error', 'Only checked-in guests can be checked out.');
        }

        DB::transaction(function () use ($reservation) {
            $reservation->update([
                'status'          => 'checked_out',
                'check_out_time'  => now()->format('H:i:s'),
                'payment_status'  => $reservation->balance_due <= 0 ? 'paid' : $reservation->payment_status,
            ]);
            $reservation->room->update(['status' => 'cleaning']);
        });

        return back()->with('success', 'Guest checked out successfully! Room status set to cleaning.');
    }

    // Cancel
    public function cancel(Reservation $reservation)
    {
        if (in_array($reservation->status, ['checked_out', 'cancelled'])) {
            return back()->with('error', 'Cannot cancel this reservation.');
        }

        DB::transaction(function () use ($reservation) {
            if ($reservation->status === 'checked_in') {
                $reservation->room->update(['status' => 'cleaning']);
            }
            $reservation->update(['status' => 'cancelled']);
        });

        return back()->with('success', 'Reservation cancelled.');
    }

    // Add Payment
    public function addPayment(Request $request, Reservation $reservation)
    {
        $data = $request->validate([
            'amount'         => 'required|numeric|min:0.01',
            'method'         => 'required|string',
            'transaction_id' => 'nullable|string',
            'notes'          => 'nullable|string',
        ]);

        DB::transaction(function () use ($data, $reservation) {
            Payment::create(array_merge($data, [
                'reservation_id' => $reservation->id,
                'type'           => 'payment',
                'paid_at'        => now(),
                'received_by'    => auth()->id(),
            ]));

            $newPaid = $reservation->paid_amount + $data['amount'];
            $status  = match(true) {
                $newPaid <= 0                     => 'unpaid',
                $newPaid < $reservation->total_amount => 'partial',
                default                           => 'paid',
            };

            $reservation->update([
                'paid_amount'    => $newPaid,
                'payment_status' => $status,
                'payment_method' => $data['method'],
            ]);
        });

        return back()->with('success', 'Payment recorded successfully!');
    }
}
