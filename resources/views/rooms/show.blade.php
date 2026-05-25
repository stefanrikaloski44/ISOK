@extends('layouts.app')
@section('page-title', 'Room ' . $room->room_number)
@section('breadcrumbs')
    <a href="{{ route('admin.rooms.index') }}" class="hover:text-gray-600">Rooms</a> / {{ $room->room_number }}
@endsection

@section('header-actions')
    <a href="{{ route('admin.rooms.edit', $room) }}" class="inline-flex items-center gap-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg transition-colors">
        Edit Room
    </a>
    @if($room->status === 'available')
        @if($room->status === 'available' && !auth()->user()->is_admin)
            <a href="{{ route('admin.reservations.create', ['room_id' => $room->id]) }}"
               class="inline-flex items-center gap-2 bg-hotel-600 hover:bg-hotel-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                Book Room
            </a>
        @endif
    @endif
@endsection

@section('content')
    <div class="grid grid-cols-3 gap-6">
        <!-- Room Details -->
        <div class="col-span-1 space-y-4">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">{{ $room->room_number }}</h2>
                        <p class="text-gray-500 capitalize">{{ $room->type }} Room</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $room->status_badge }}">
                    {{ ucfirst($room->status) }}
                </span>
                </div>

                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Floor</dt>
                        <dd class="font-medium text-gray-900">{{ $room->floor }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Capacity</dt>
                        <dd class="font-medium text-gray-900">{{ $room->capacity }} guests</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Price / Night</dt>
                        <dd class="font-bold text-hotel-700">${{ number_format($room->price_per_night, 2) }}</dd>
                    </div>
                </dl>

                @if($room->description)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-sm text-gray-600">{{ $room->description }}</p>
                    </div>
                @endif

                @if($room->amenities && count($room->amenities) > 0)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Amenities</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($room->amenities as $amenity)
                                <span class="bg-hotel-50 text-hotel-700 text-xs px-2 py-1 rounded-full">{{ $amenity }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Reservations -->
        <div class="col-span-2 space-y-4">
            <!-- Upcoming -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Upcoming Reservations</h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($upcomingReservations as $res)
                        <div class="flex items-center gap-4 px-6 py-3">
                            <div class="flex-1">
                                <a href="{{ route('admin.guests.show', $res->guest) }}" class="text-sm font-medium text-gray-900 hover:text-hotel-700">
                                    {{ $res->guest->full_name }}
                                </a>
                                <p class="text-xs text-gray-400">{{ $res->check_in_date->format('M d') }} → {{ $res->check_out_date->format('M d, Y') }} · {{ $res->nights }} nights</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $res->status_badge }}">
                        {{ ucfirst($res->status) }}
                    </span>
                            <a href="{{ route('reservations.show', $res) }}" class="text-xs text-hotel-600 hover:underline">View</a>
                        </div>
                    @empty
                        <div class="px-6 py-6 text-center text-sm text-gray-400">No upcoming reservations</div>
                    @endforelse
                </div>
            </div>

            <!-- Recent History -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Recent History</h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($recentHistory as $res)
                        <div class="flex items-center gap-4 px-6 py-3">
                            <div class="flex-1">
                                <p class="text-xs text-gray-400">{{ $res->check_in_date->format('M d') }} → {{ $res->check_out_date->format('M d, Y') }}</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $res->status_badge }}">
                        {{ ucfirst($res->status) }}
                    </span>
                            <p class="text-xs font-medium text-gray-600">${{ number_format($res->total_amount, 0) }}</p>
                        </div>
                    @empty
                        <div class="px-6 py-6 text-center text-sm text-gray-400">No history yet</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
