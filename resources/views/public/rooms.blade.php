@extends('layouts.public')
@section('title', 'Rooms — Grand Horizon')
@section('content')
    <div class="max-w-6xl mx-auto px-6 py-12">
        <h1 class="font-display text-4xl font-semibold text-gray-900 mb-2">Our Rooms</h1>
        <p class="text-gray-500 mb-8">Choose from our selection of carefully appointed rooms</p>

        <form method="GET" class="flex flex-wrap gap-3 mb-8 bg-white p-4 rounded-xl border border-gray-100">
            <select name="type" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400">
                <option value="">All Types</option>
                @foreach(\App\Models\Room::types() as $type)
                    <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                @endforeach
            </select>
            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min price"
                   class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-28 focus:outline-none focus:ring-2 focus:ring-hotel-400">
            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max price"
                   class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-28 focus:outline-none focus:ring-2 focus:ring-hotel-400">

            <div class="flex items-center gap-2">
                <div class="flex flex-col">
                    <label class="text-xs text-gray-400 mb-0.5 pl-1">Check-in</label>
                    <input type="date" name="check_in" value="{{ request('check_in', today()->toDateString()) }}"
                           min="{{ today()->toDateString() }}"
                           class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400">
                </div>
                <span class="text-gray-300 mt-4">→</span>
                <div class="flex flex-col">
                    <label class="text-xs text-gray-400 mb-0.5 pl-1">Check-out</label>
                    <input type="date" name="check_out" value="{{ request('check_out', today()->toDateString()) }}"
                           min="{{ today()->toDateString() }}"
                           class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400">
                </div>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="bg-hotel-600 hover:bg-hotel-700 text-white text-sm px-5 py-2 rounded-lg transition-colors">
                    Search
                </button>
                @if(request()->hasAny(['type','min_price','max_price','check_in','check_out']))
                    <a href="{{ route('public.rooms') }}" class="border border-gray-200 text-gray-500 text-sm px-4 py-2 rounded-lg hover:bg-gray-50">Clear</a>
                @endif
            </div>

            @if(request('check_in') && request('check_out') && request('check_in') !== request('check_out'))
                <div class="w-full mt-1">
                    <p class="text-xs text-hotel-600">
                        @php $nights = \Carbon\Carbon::parse(request('check_in'))->diffInDays(\Carbon\Carbon::parse(request('check_out'))); @endphp
                        Showing rooms available
                        <strong>{{ \Carbon\Carbon::parse(request('check_in'))->format('M d') }}</strong> →
                        <strong>{{ \Carbon\Carbon::parse(request('check_out'))->format('M d, Y') }}</strong>
                        ({{ $nights }} night{{ $nights > 1 ? 's' : '' }})
                    </p>
                </div>
            @endif
        </form>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($rooms as $room)
                @php
                    $bookQuery = request('check_in') ? '?check_in='.request('check_in').'&check_out='.request('check_out') : '';
                @endphp
                <a href="{{ route('public.room', $room) . $bookQuery }}"
                   class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-all group overflow-hidden">

                    {{-- First photo only --}}
                    @if($room->image_url)
                        <img src="{{ $room->image_url }}" alt="{{ ucfirst($room->type) }} room"
                             class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-48 bg-gradient-to-br from-hotel-100 to-hotel-200 flex items-center justify-center">
                            <svg class="w-12 h-12 text-hotel-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                            </svg>
                        </div>
                    @endif

                    {{-- Photo count badge --}}
                    @if(count($room->image_urls) > 1)
                        <div class="relative">
                <span class="absolute bottom-2 right-2 -mt-10 bg-black bg-opacity-50 text-white text-xs
                             px-2 py-0.5 rounded-full flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01"/>
                    </svg>
                    {{ count($room->image_urls) }}
                </span>
                        </div>
                    @endif

                    <div class="p-5">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="text-xl font-bold text-gray-900 capitalize">{{ ucfirst($room->type) }} Room</p>
                                <p class="text-xs text-gray-400">Floor {{ $room->floor }} · Up to {{ $room->capacity }} guests</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-hotel-700">${{ number_format($room->price_per_night, 0) }}</p>
                                <p class="text-xs text-gray-400">per night</p>
                            </div>
                        </div>
                        @if($room->description)
                            <p class="text-sm text-gray-500 my-3 line-clamp-2">{{ $room->description }}</p>
                        @endif
                        @if($room->amenities)
                            <div class="flex flex-wrap gap-1 mb-4">
                                @foreach(array_slice($room->amenities, 0, 4) as $amenity)
                                    <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded">{{ $amenity }}</span>
                                @endforeach
                            </div>
                        @endif
                        <div class="flex items-center justify-end">
                            <span class="text-sm text-hotel-600 font-medium group-hover:underline">View details →</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full py-16 text-center text-gray-400">
                    @if(request('check_in') && request('check_out') && request('check_in') !== request('check_out'))
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="font-medium text-gray-500">No rooms available for these dates</p>
                        <p class="text-sm mt-1">Try different dates or fewer filters</p>
                    @else
                        No rooms found matching your filters.
                    @endif
                </div>
            @endforelse
        </div>

        <div class="mt-6">{{ $rooms->links() }}</div>
    </div>

    @push('scripts')
        <script>
            const checkIn  = document.querySelector('input[name="check_in"]');
            const checkOut = document.querySelector('input[name="check_out"]');
            if (checkIn && checkOut) {
                checkIn.addEventListener('change', () => {
                    if (checkOut.value <= checkIn.value) {
                        const next = new Date(checkIn.value);
                        next.setDate(next.getDate() + 1);
                        checkOut.value = next.toISOString().split('T')[0];
                    }
                    checkOut.min = checkIn.value;
                });
            }
        </script>
    @endpush
@endsection
