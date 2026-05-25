<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\Guest;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Totals
        $totalRooms         = Room::count();
        $availableRooms     = Room::where('status', 'available')->count();
        $occupiedRooms      = Room::where('status', 'occupied')->count();
        $totalGuests        = Guest::count();

        // Today
        $checkInsToday      = Reservation::whereDate('check_in_date', today())
            ->whereNotIn('status', ['cancelled', 'no_show'])->count();
        $checkOutsToday     = Reservation::whereDate('check_out_date', today())
            ->whereNotIn('status', ['cancelled', 'no_show'])->count();

        // Revenue
        $revenueThisMonth   = Payment::where('type', 'payment')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        // Active reservations
        $activeReservations = Reservation::with(['room', 'guest'])
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->orderBy('check_in_date')
            ->take(10)
            ->get();

        // Upcoming check-ins (next 7 days)
        $upcomingCheckIns   = Reservation::with(['room', 'guest'])
            ->where('status', 'confirmed')
            ->whereBetween('check_in_date', [today(), today()->addDays(7)])
            ->orderBy('check_in_date')
            ->take(5)
            ->get();

        // Room type breakdown
        $roomTypeStats = Room::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->get();

        // Monthly revenue (last 6 months)
        $monthlyRevenue = Payment::where('type', 'payment')
            ->where('paid_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw("TO_CHAR(paid_at, 'YYYY-MM') as month"),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('dashboard.index', compact(
            'totalRooms', 'availableRooms', 'occupiedRooms', 'totalGuests',
            'checkInsToday', 'checkOutsToday', 'revenueThisMonth',
            'activeReservations', 'upcomingCheckIns', 'roomTypeStats', 'monthlyRevenue'
        ));
    }
}
