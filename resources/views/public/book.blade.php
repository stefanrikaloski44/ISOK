@extends('layouts.public')
@section('title', 'Book Room ' . $room->room_number)
@section('content')
    <div class="max-w-4xl mx-auto px-6 py-12">
        <a href="{{ route('public.room', $room) }}" class="text-sm text-gray-500 hover:text-hotel-600 mb-6 inline-block">← Back to room</a>

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm mb-6">{{ session('error') }}</div>
        @endif

        <div class="grid grid-cols-3 gap-8">
            <div class="col-span-2">
                <h1 class="font-display text-3xl font-semibold text-gray-900 mb-2">Book Room {{ $room->room_number }}</h1>
                <p class="text-gray-500 mb-8 capitalize">{{ $room->type }} · ${{ number_format($room->price_per_night, 2) }}/night</p>

                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <form method="POST" action="{{ route('public.book.store', $room) }}" class="space-y-5" id="bookForm">
                        @csrf
                        <p class="font-medium text-gray-900 text-sm">Your details</p>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
                                <input type="text" name="first_name" value="{{ old('first_name', auth()->user()?->name ? explode(' ', auth()->user()->name)[0] : '') }}" required
                                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}" required
                                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                                <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required
                                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input type="text" name="phone" value="{{ old('phone') }}"
                                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400">
                            </div>
                        </div>

                        <hr class="border-gray-100">
                        <p class="font-medium text-gray-900 text-sm">Stay details</p>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Check-in <span class="text-red-500">*</span></label>
                                <input type="date" name="check_in_date" id="checkIn"
                                       value="{{ old('check_in_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required
                                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Check-out <span class="text-red-500">*</span></label>
                                <input type="date" name="check_out_date" id="checkOut"
                                       value="{{ old('check_out_date', date('Y-m-d', strtotime('+1 day'))) }}" required
                                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Number of guests</label>
                            <input type="number" name="guests_count" value="{{ old('guests_count', 1) }}" min="1" max="{{ $room->capacity }}"
                                   class="w-32 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400">
                            <span class="text-xs text-gray-400 ml-2">Max {{ $room->capacity }}</span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Special requests</label>
                            <textarea name="special_requests" rows="2"
                                      class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400"
                                      placeholder="Any special requests or preferences...">{{ old('special_requests') }}</textarea>
                        </div>
                        <button type="submit"
                                class="w-full bg-hotel-600 hover:bg-hotel-700 text-white font-medium py-3 rounded-lg transition-colors">
                            Confirm Reservation
                        </button>
                        @guest
                            <p class="text-xs text-gray-400 text-center">
                                <a href="{{ route('login') }}" class="text-hotel-600 hover:underline">Sign in</a> to manage your reservations easily
                            </p>
                        @endguest
                    </form>
                </div>
            </div>

            <!-- Summary sidebar -->
            <div class="space-y-4">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 sticky top-24">
                    <p class="font-semibold text-gray-900 mb-4">Booking Summary</p>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Room</span>
                            <span class="font-medium">{{ $room->room_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Type</span>
                            <span class="capitalize">{{ $room->type }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Rate</span>
                            <span class="font-medium text-hotel-700">${{ number_format($room->price_per_night, 2) }}/night</span>
                        </div>
                        <hr class="border-gray-100 my-3">
                        <div class="flex justify-between text-xs text-gray-400" id="nightsRow">
                            <span>Nights</span><span id="nightsCount">—</span>
                        </div>
                        <div class="flex justify-between font-bold text-gray-900 pt-1">
                            <span>Total</span><span id="totalAmount">—</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const price = {{ $room->price_per_night }};
        function updateTotal() {
            const ci = document.getElementById('checkIn').value;
            const co = document.getElementById('checkOut').value;
            if (!ci || !co) return;
            const nights = Math.max(0, Math.round((new Date(co) - new Date(ci)) / 86400000));
            document.getElementById('nightsCount').textContent = nights;
            document.getElementById('totalAmount').textContent = nights > 0 ? '$' + (nights * price).toFixed(2) : '—';
        }
        document.getElementById('checkIn').addEventListener('change', updateTotal);
        document.getElementById('checkOut').addEventListener('change', updateTotal);
        updateTotal();
    </script>
@endsection
