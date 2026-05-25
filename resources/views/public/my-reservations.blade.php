@extends('layouts.public')
@section('title', 'My Reservations')
@section('content')
    <div class="max-w-4xl mx-auto px-6 py-12">
        <h1 class="font-display text-3xl font-semibold text-gray-900 mb-2">My Reservations</h1>
        <p class="text-gray-500 mb-8">All bookings associated with your email address</p>

        @if($reservations instanceof \Illuminate\Pagination\LengthAwarePaginator ? $reservations->isEmpty() : $reservations->isEmpty())
            <div class="bg-white rounded-xl border border-gray-100 p-16 text-center text-gray-400">
                <p class="mb-3">No reservations yet</p>
                <a href="/rooms" class="text-hotel-600 hover:underline text-sm">Browse available rooms</a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($reservations as $res)
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-5">
                        <div class="w-12 h-12 rounded-xl bg-hotel-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-bold text-hotel-700">{{ $res->room->room_number }}</span>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <p class="font-mono text-sm font-medium text-hotel-700">{{ $res->reservation_number }}</p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $res->status_badge }}">
                                {{ ucwords(str_replace('_', ' ', $res->status)) }}
                            </span>
                            </div>
                            <p class="text-sm text-gray-600">Room {{ $res->room->room_number }} ({{ ucfirst($res->room->type) }})</p>
                            <p class="text-xs text-gray-400">{{ $res->check_in_date->format('M d') }} → {{ $res->check_out_date->format('M d, Y') }} · {{ $res->nights }} nights</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-gray-900">${{ number_format($res->total_amount, 2) }}</p>
                            <span class="text-xs {{ $res->payment_status === 'paid' ? 'text-green-600' : 'text-yellow-600' }}">
                            {{ ucfirst($res->payment_status) }}
                        </span>
                        </div>
                    </div>
                @endforeach
            </div>
            @if(method_exists($reservations, 'links'))
                <div class="mt-6">{{ $reservations->links() }}</div>
            @endif
        @endif
    </div>
@endsection
