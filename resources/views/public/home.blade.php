@extends('layouts.public')
@section('title', 'Grand Horizon — Luxury Hotel')
@section('content')
    <div class="bg-gray-900 text-white py-24 px-6">
        <div class="max-w-3xl mx-auto text-center">
            <p class="text-hotel-400 text-sm tracking-widest uppercase mb-4">Grand Horizon Hotel</p>
            <h1 class="font-display text-5xl font-bold mb-6 leading-tight">Your perfect stay<br>starts here</h1>
            <p class="text-gray-400 mb-8">Luxury rooms, exceptional service, unforgettable experiences.</p>
            <a href="/rooms"
               class="inline-block bg-hotel-600 hover:bg-hotel-700 text-white px-8 py-3 rounded-lg font-medium transition-colors">
                Browse Rooms
            </a>
        </div>
    </div>
    <div class="max-w-6xl mx-auto px-6 py-16">
        <h2 class="font-display text-3xl font-semibold text-gray-900 mb-8">Available Rooms</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($rooms as $room)
                <a href="/rooms/{{ $room->id }}"
                   class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow overflow-hidden group">
                    <div class="h-2 w-full {{ match($room->status) { 'available'=>'bg-green-400', default=>'bg-gray-300' } }}"></div>
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ $room->room_number }}</p>
                                <p class="text-xs text-gray-400 capitalize">{{ $room->type }} · Floor {{ $room->floor }}</p>
                            </div>
                            <span class="text-hotel-700 font-bold">${{ number_format($room->price_per_night, 0) }}<span class="text-xs font-normal text-gray-400">/night</span></span>
                        </div>
                        @if($room->description)
                            <p class="text-sm text-gray-500 mb-3 line-clamp-2">{{ $room->description }}</p>
                        @endif
                        <span class="inline-block bg-hotel-50 text-hotel-700 text-xs px-3 py-1 rounded-full font-medium group-hover:bg-hotel-100 transition-colors">
                        Book Now →
                    </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endsection
