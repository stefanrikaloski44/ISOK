@extends('layouts.public')
@section('title', ucfirst($room->type) . ' Room — Grand Horizon')
@section('content')
    <div class="max-w-6xl mx-auto px-6 py-12">

        <a href="{{ route('public.rooms') }}{{ $checkIn ? '?check_in='.$checkIn.'&check_out='.$checkOut : '' }}"
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-hotel-600 mb-8 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to all rooms
        </a>

        @php $imageUrls = $room->image_urls; @endphp

        {{-- ── Photo slideshow ── --}}
        @if(count($imageUrls) > 0)
            <div class="w-full h-[480px] rounded-2xl overflow-hidden mb-8 shadow-sm relative" id="slideshow">

                {{-- Slides --}}
                @foreach($imageUrls as $i => $url)
                    <div class="slide absolute inset-0 transition-opacity duration-500 {{ $i === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}"
                         data-index="{{ $i }}">
                        <img src="{{ $url }}" alt="Room photo {{ $i + 1 }}" class="w-full h-full object-cover">
                    </div>
                @endforeach

                {{-- Arrows (only if more than 1 photo) --}}
                @if(count($imageUrls) > 1)
                    <button onclick="slideChange(-1)"
                            class="absolute left-3 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-full bg-black bg-opacity-40
                       hover:bg-opacity-60 text-white flex items-center justify-center transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <button onclick="slideChange(1)"
                            class="absolute right-3 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-full bg-black bg-opacity-40
                       hover:bg-opacity-60 text-white flex items-center justify-center transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>

                    {{-- Dot indicators --}}
                    <div class="absolute bottom-3 left-1/2 -translate-x-1/2 z-20 flex items-center gap-1.5">
                        @foreach($imageUrls as $i => $url)
                            <button onclick="slideTo({{ $i }})"
                                    id="dot-{{ $i }}"
                                    class="dot w-2 h-2 rounded-full transition-all {{ $i === 0 ? 'bg-white scale-110' : 'bg-white bg-opacity-50' }}">
                            </button>
                        @endforeach
                    </div>

                    {{-- Counter --}}
                    <div class="absolute top-3 right-3 z-20 bg-black bg-opacity-40 text-white text-xs
                    px-2.5 py-1 rounded-full" id="slideCounter">
                        1 / {{ count($imageUrls) }}
                    </div>
                @endif
            </div>

        @else
            {{-- Fallback placeholder --}}
            <div class="w-full h-[480px] rounded-2xl mb-8 bg-gradient-to-br from-hotel-100 to-hotel-200
                flex items-center justify-center shadow-sm">
                <svg class="w-16 h-16 text-hotel-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                </svg>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- ── Left: Room info ── --}}
            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-widest mb-1">{{ $room->type }} Room</p>
                            <h1 class="font-display text-4xl font-bold text-gray-900 capitalize">{{ ucfirst($room->type) }} Room</h1>
                            <p class="text-gray-500 mt-1">Floor {{ $room->floor }} &middot; Up to {{ $room->capacity }} guests</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-3xl font-bold text-hotel-700">${{ number_format($room->price_per_night, 0) }}</p>
                            <p class="text-xs text-gray-400">per night</p>
                        </div>
                    </div>
                    @if($room->description)
                        <p class="text-gray-600 leading-relaxed">{{ $room->description }}</p>
                    @endif
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
                    <h2 class="font-display text-xl font-semibold text-gray-900 mb-6">Room Details</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-6">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 bg-hotel-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-hotel-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Capacity</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $room->capacity }} guests</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 bg-hotel-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-hotel-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Floor</p>
                                <p class="text-sm font-semibold text-gray-900">Floor {{ $room->floor }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 bg-hotel-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-hotel-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Price</p>
                                <p class="text-sm font-semibold text-gray-900">${{ number_format($room->price_per_night, 2) }}/night</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($room->amenities && count($room->amenities) > 0)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
                        <h2 class="font-display text-xl font-semibold text-gray-900 mb-6">Amenities</h2>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach($room->amenities as $amenity)
                                <div class="flex items-center gap-2.5 text-sm text-gray-700">
                                    <div class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    {{ $amenity }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- ── Right: Booking card ── --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sticky top-24">
                    <p class="font-display text-xl font-semibold text-gray-900 mb-1">
                        ${{ number_format($room->price_per_night, 0) }}
                        <span class="text-sm font-normal text-gray-400">/ night</span>
                    </p>
                    <p class="text-xs text-gray-400 mb-6">Taxes and fees included</p>

                    @if($room->status === 'maintenance')
                        <div class="w-full text-center bg-gray-100 text-gray-400 font-medium py-3 rounded-xl mb-4 cursor-not-allowed">
                            Under Maintenance
                        </div>
                        <p class="text-xs text-gray-400 text-center">This room is currently unavailable</p>

                    @elseif(auth()->check() && auth()->user()->is_admin)
                        {{-- Admins see a neutral info block, no Book button --}}
                        <div class="w-full text-center bg-hotel-50 text-hotel-700 font-medium py-3 rounded-xl mb-4 text-sm">
                            Admin View
                        </div>
                        <p class="text-xs text-gray-400 text-center">
                            <a href="{{ route('admin.rooms.edit', $room) }}" class="text-hotel-600 hover:underline">Edit this room</a>
                            in the admin panel
                        </p>

                    @else
                        @php
                            $bookUrl = route('public.book', $room);
                            if ($checkIn && $checkOut) {
                                $bookUrl .= '?check_in='.$checkIn.'&check_out='.$checkOut;
                            }
                        @endphp
                        <a href="{{ $bookUrl }}"
                           class="block w-full text-center bg-hotel-600 hover:bg-hotel-700 text-white font-medium py-3 rounded-xl transition-colors mb-4">
                            Book This Room
                        </a>
                        @if($checkIn && $checkOut && $checkIn !== $checkOut)
                            @php $nights = \Carbon\Carbon::parse($checkIn)->diffInDays(\Carbon\Carbon::parse($checkOut)); @endphp
                            <p class="text-xs text-hotel-600 text-center mb-3 font-medium">
                                {{ $nights }} night{{ $nights > 1 ? 's' : '' }} ·
                                ${{ number_format($room->price_per_night * $nights, 0) }} total
                            </p>
                        @endif
                        <p class="text-xs text-gray-400 text-center">Availability checked at time of booking</p>
                    @endif

                    <hr class="border-gray-100 my-5">

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Type</span>
                            <span class="font-medium text-gray-900 capitalize">{{ $room->type }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Max guests</span>
                            <span class="font-medium text-gray-900">{{ $room->capacity }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Floor</span>
                            <span class="font-medium text-gray-900">{{ $room->floor }}</span>
                        </div>
                    </div>

                    @guest
                        <hr class="border-gray-100 my-5">
                        <p class="text-xs text-gray-400 text-center">
                            <a href="{{ route('login') }}" class="text-hotel-600 hover:underline font-medium">Sign in</a>
                            to track your reservations
                        </p>
                    @endguest
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            let current = 0;
            const total = {{ count($imageUrls) }};

            function slideTo(index) {
                if (total <= 1) return;

                // Hide current
                document.querySelectorAll('.slide')[current].classList.replace('opacity-100', 'opacity-0');
                document.querySelectorAll('.slide')[current].classList.replace('z-10', 'z-0');
                document.querySelectorAll('.dot')[current].classList.remove('bg-white', 'scale-110');
                document.querySelectorAll('.dot')[current].classList.add('bg-white', 'bg-opacity-50');

                // Show new
                current = (index + total) % total;
                document.querySelectorAll('.slide')[current].classList.replace('opacity-0', 'opacity-100');
                document.querySelectorAll('.slide')[current].classList.replace('z-0', 'z-10');
                document.querySelectorAll('.dot')[current].classList.remove('bg-opacity-50');
                document.querySelectorAll('.dot')[current].classList.add('scale-110');

                const counter = document.getElementById('slideCounter');
                if (counter) counter.textContent = (current + 1) + ' / ' + total;
            }

            function slideChange(direction) {
                slideTo(current + direction);
            }

            // Keyboard navigation
            document.addEventListener('keydown', e => {
                if (e.key === 'ArrowLeft')  slideChange(-1);
                if (e.key === 'ArrowRight') slideChange(1);
            });
        </script>
    @endpush
@endsection
