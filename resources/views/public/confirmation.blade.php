@extends('layouts.public')
@section('title', 'Booking Confirmed')
@section('content')
    <div class="max-w-lg mx-auto px-6 py-16 text-center">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="font-display text-3xl font-semibold text-gray-900 mb-2">Booking Confirmed!</h1>
        <p class="text-gray-500 mb-8">Your reservation has been successfully created.</p>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 text-left space-y-3 text-sm mb-8">
            <div class="flex justify-between">
                <span class="text-gray-500">Reservation #</span>
                <span class="font-mono font-bold text-hotel-700">{{ $reservation->reservation_number }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Guest</span>
                <span class="font-medium">{{ $reservation->guest->full_name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Room</span>
                <span>{{ $reservation->room->room_number }} ({{ ucfirst($reservation->room->type) }})</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Check-in</span>
                <span>{{ $reservation->check_in_date->format('M d, Y') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Check-out</span>
                <span>{{ $reservation->check_out_date->format('M d, Y') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Nights</span>
                <span>{{ $reservation->nights }}</span>
            </div>
            <hr class="border-gray-100">
            <div class="flex justify-between font-bold text-gray-900">
                <span>Total</span>
                <span>${{ number_format($reservation->total_amount, 2) }}</span>
            </div>
        </div>

        <div class="flex gap-3 justify-center">
            <a href="/rooms" class="border border-gray-200 text-gray-700 px-6 py-2.5 rounded-lg text-sm hover:bg-gray-50 transition-colors">
                Browse More Rooms
            </a>
            @auth
                <a href="{{ route('my.reservations') }}" class="bg-hotel-600 hover:bg-hotel-700 text-white px-6 py-2.5 rounded-lg text-sm transition-colors">
                    My Reservations
                </a>
            @endauth
        </div>
    </div>
@endsection
