@extends('layouts.app')
@section('page-title', 'Dashboard')

@section('header-actions')

@endsection

@section('content')

    {{-- Stats --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm text-gray-500 font-medium">Total Rooms</p>
                <div class="w-9 h-9 bg-hotel-50 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-hotel-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-1M3 7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v1M3 7h14"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $totalRooms }}</p>
            <div class="flex items-center gap-3 mt-2">
            <span class="inline-flex items-center gap-1 text-xs text-green-600 font-medium">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                {{ $availableRooms }} available
            </span>
                <span class="inline-flex items-center gap-1 text-xs text-red-500 font-medium">
                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                {{ $occupiedRooms }} occupied
            </span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm text-gray-500 font-medium">Total Guests</p>
                <div class="w-9 h-9 bg-blue-50 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $totalGuests }}</p>
            <p class="text-xs text-gray-400 mt-2">Registered in system</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm text-gray-500 font-medium">Today</p>
                <div class="w-9 h-9 bg-purple-50 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $checkInsToday + $checkOutsToday }}</p>
            <div class="flex items-center gap-3 mt-2">
                <span class="text-xs text-gray-500">↓ {{ $checkInsToday }} check-ins</span>
                <span class="text-xs text-gray-500">↑ {{ $checkOutsToday }} check-outs</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm text-gray-500 font-medium">Revenue</p>
                <div class="w-9 h-9 bg-green-50 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">${{ number_format($revenueThisMonth, 0) }}</p>
            <p class="text-xs text-gray-400 mt-2">{{ now()->format('F Y') }}</p>
        </div>
    </div>

    {{-- Occupancy bar --}}
    @if($totalRooms > 0)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-semibold text-gray-700">Room Occupancy</p>
                <p class="text-sm font-bold text-gray-900">{{ round($occupiedRooms / $totalRooms * 100) }}%</p>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2.5">
                <div class="bg-hotel-600 h-2.5 rounded-full transition-all duration-500"
                     style="width: {{ round($occupiedRooms / $totalRooms * 100) }}%"></div>
            </div>
            <div class="flex items-center justify-between mt-3 text-xs text-gray-400">
                <span>{{ $occupiedRooms }} occupied</span>
                <span>{{ $availableRooms }} available</span>
                <span>{{ $totalRooms - $occupiedRooms - $availableRooms }} other</span>
            </div>
        </div>
    @endif

    {{-- Main grid --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- Active Reservations --}}
        <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50">
                <div>
                    <h2 class="font-semibold text-gray-900">Active Reservations</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Confirmed & checked-in guests</p>
                </div>
                <a href="{{ route('admin.reservations.index', ['status' => 'checked_in']) }}"
                   class="text-xs text-hotel-600 hover:text-hotel-700 font-medium hover:underline">
                    View all →
                </a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($activeReservations as $res)
                    <div class="flex items-center gap-4 px-6 py-3.5 hover:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 rounded-xl bg-hotel-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-xs font-bold text-hotel-700">{{ $res->room->room_number }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900">{{ $res->guest->full_name }}</p>
                            <p class="text-xs text-gray-400">
                                {{ $res->check_in_date->format('M d') }} → {{ $res->check_out_date->format('M d') }}
                                · {{ $res->nights }} nights
                            </p>
                        </div>
                        <div class="text-right flex-shrink-0">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $res->status_badge }}">
                            {{ ucfirst(str_replace('_', ' ', $res->status)) }}
                        </span>
                            <p class="text-xs text-gray-400 mt-1">${{ number_format($res->total_amount, 0) }}</p>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm text-gray-400">No active reservations</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Right column --}}
        <div class="space-y-6">

            {{-- Upcoming check-ins --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-50">
                    <h2 class="font-semibold text-gray-900 text-sm">Upcoming Check-ins</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Next 7 days</p>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($upcomingCheckIns as $res)
                        <div class="px-5 py-3 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900">{{ $res->guest->full_name }}</p>
                                <span class="text-xs font-semibold text-hotel-700">
                                {{ $res->check_in_date->format('M d') }}
                            </span>
                            </div>
                            <p class="text-xs text-gray-400 mt-0.5">
                                Room {{ $res->room->room_number }} · {{ $res->nights }} nights
                            </p>
                        </div>
                    @empty
                        <div class="px-5 py-6 text-center text-xs text-gray-400">No upcoming check-ins</div>
                    @endforelse
                </div>
            </div>

            {{-- Room type breakdown --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h2 class="font-semibold text-gray-900 text-sm mb-4">Rooms by Type</h2>
                <div class="space-y-3">
                    @foreach($roomTypeStats as $stat)
                        <div class="flex items-center justify-between">
                            <span class="text-sm capitalize text-gray-600">{{ $stat->type }}</span>
                            <div class="flex items-center gap-3">
                                <div class="w-24 bg-gray-100 rounded-full h-1.5">
                                    <div class="bg-hotel-500 h-1.5 rounded-full"
                                         style="width: {{ $totalRooms > 0 ? round($stat->count / $totalRooms * 100) : 0 }}%"></div>
                                </div>
                                <span class="text-xs font-semibold text-gray-700 w-4 text-right">{{ $stat->count }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Quick actions --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h2 class="font-semibold text-gray-900 text-sm mb-4">Quick Actions</h2>
                <div class="space-y-1">
                    <a href="{{ route('admin.rooms.create') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-600 hover:bg-hotel-50 hover:text-hotel-700 transition-colors group">
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-hotel-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Room
                    </a>
                    <a href="{{ route('admin.reservations.index', ['status' => 'pending']) }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-600 hover:bg-hotel-50 hover:text-hotel-700 transition-colors group">
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-hotel-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Pending Reservations
                    </a>
                </div>
            </div>

        </div>
    </div>

@endsection
