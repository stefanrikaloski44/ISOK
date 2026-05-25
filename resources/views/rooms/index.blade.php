@extends('layouts.app')
@section('page-title', 'Rooms')

@section('header-actions')
    <a href="{{ route('admin.rooms.create') }}"
       class="inline-flex items-center gap-2 bg-hotel-600 hover:bg-hotel-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Room
    </a>
@endsection

@section('content')
    <!-- Filters -->
    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search room number..."
               class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400 w-52">
        <select name="type" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400">
            <option value="">All Types</option>
            @foreach(\App\Models\Room::types() as $type)
                <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
            @endforeach
        </select>
        <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400">
            <option value="">All Statuses</option>
            @foreach(\App\Models\Room::statuses() as $status)
                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                    {{ ucwords(str_replace('_', ' ', $status)) }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            Filter
        </button>
        @if(request()->hasAny(['search','type','status']))
            <a href="{{ route('admin.rooms.index') }}"
               class="bg-white border border-gray-200 text-gray-500 text-sm px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                Clear
            </a>
        @endif
    </form>

    <!-- Rooms Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($rooms as $room)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow overflow-hidden">

                @if($room->image_url)
                    <div class="h-40 overflow-hidden">
                        <img src="{{ $room->image_url }}"
                             alt="Room {{ $room->room_number }}"
                             class="w-full h-full object-cover">
                    </div>
                @else
                    <div class="h-40 bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center">
                        <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                        </svg>
                    </div>
                @endif

                <div class="p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ $room->room_number }}</p>
                            <p class="text-xs text-gray-400 capitalize">{{ $room->type }} · Floor {{ $room->floor }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-gray-500 mb-4">
                <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ $room->capacity }} guests
                </span>
                        <span class="font-semibold text-hotel-700">${{ number_format($room->price_per_night, 0) }}/night</span>
                    </div>
                    @if($room->amenities)
                        <div class="flex flex-wrap gap-1 mb-4">
                            @foreach(array_slice($room->amenities, 0, 3) as $amenity)
                                <span class="bg-gray-100 text-gray-600 text-xs px-1.5 py-0.5 rounded">{{ $amenity }}</span>
                            @endforeach
                            @if(count($room->amenities) > 3)
                                <span class="bg-gray-100 text-gray-400 text-xs px-1.5 py-0.5 rounded">
                            +{{ count($room->amenities) - 3 }}
                        </span>
                            @endif
                        </div>
                    @endif
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.rooms.show', $room) }}"
                           class="flex-1 text-center text-xs font-medium text-hotel-700 bg-hotel-50 hover:bg-hotel-100 py-1.5 rounded-lg transition-colors">
                            View
                        </a>
                        <a href="{{ route('admin.rooms.edit', $room) }}"
                           class="flex-1 text-center text-xs font-medium text-gray-600 bg-gray-50 hover:bg-gray-100 py-1.5 rounded-lg transition-colors">
                            Edit
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                </svg>
                <p class="font-medium">No rooms found</p>
                <a href="{{ route('admin.rooms.create') }}" class="text-hotel-600 hover:underline text-sm mt-1 inline-block">
                    Add your first room
                </a>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $rooms->links() }}</div>
@endsection
