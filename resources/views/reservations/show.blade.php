@extends('layouts.app')
@section('page-title', $reservation->reservation_number)
@section('breadcrumbs')
    <a href="{{ route('admin.reservations.index') }}" class="hover:text-gray-600">Reservations</a>
    / {{ $reservation->reservation_number }}
@endsection

@section('header-actions')
    @if($reservation->status === 'confirmed')
        <form method="POST" action="{{ route('admin.reservations.check-in', $reservation) }}" class="inline">
            @csrf
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                Check In
            </button>
        </form>
    @endif
    @if($reservation->status === 'checked_in')
        <form method="POST" action="{{ route('admin.reservations.check-out', $reservation) }}" class="inline">
            @csrf
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                Check Out
            </button>
        </form>
    @endif
    @if(!in_array($reservation->status, ['checked_out', 'cancelled']))
        <form method="POST" action="{{ route('admin.reservations.cancel', $reservation) }}" class="inline"
              onsubmit="return confirm('Cancel this reservation?')">
            @csrf
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-white border border-red-200 hover:bg-red-50 text-red-600 text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                Cancel
            </button>
        </form>
    @endif
@endsection

@section('content')
    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-3 gap-6">

        {{-- Left: main info --}}
        <div class="col-span-2 space-y-5">

            {{-- Overview card --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-start justify-between mb-5">
                    <div>
                        <p class="font-mono text-lg font-bold text-hotel-700">{{ $reservation->reservation_number }}</p>
                        <p class="text-xs text-gray-400">Created {{ $reservation->created_at->format('M d, Y \a\t H:i') }}</p>
                    </div>
                    <div class="flex gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $reservation->status_badge }}">
                            {{ ucwords(str_replace('_', ' ', $reservation->status)) }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $reservation->payment_status_badge }}">
                            {{ ucfirst($reservation->payment_status) }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-6">
                    {{-- Guest --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Guest</p>
                        <p class="font-medium text-gray-900">{{ $reservation->guest->full_name }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $reservation->guest->email }}</p>
                        <p class="text-xs text-gray-500">{{ $reservation->guest->phone }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $reservation->guests_count }} guest(s)</p>
                    </div>

                    {{-- Room --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Room</p>
                        <a href="{{ route('admin.rooms.show', $reservation->room) }}"
                           class="font-medium text-gray-900 hover:text-hotel-700">
                            Room {{ $reservation->room->room_number }}
                        </a>
                        <p class="text-xs text-gray-500 mt-0.5 capitalize">
                            {{ $reservation->room->type }} — Floor {{ $reservation->room->floor }}
                        </p>
                        <p class="text-xs text-gray-500">Capacity: {{ $reservation->room->capacity }}</p>
                    </div>

                    {{-- Stay --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Stay</p>
                        <p class="font-medium text-gray-900">{{ $reservation->check_in_date->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-500">→ {{ $reservation->check_out_date->format('M d, Y') }}</p>
                        <p class="text-xs text-hotel-600 font-medium mt-1">{{ $reservation->nights }} night(s)</p>
                        @if($reservation->check_in_time)
                            <p class="text-xs text-gray-400 mt-1">Checked in: {{ $reservation->check_in_time }}</p>
                        @endif
                        @if($reservation->check_out_time)
                            <p class="text-xs text-gray-400">Checked out: {{ $reservation->check_out_time }}</p>
                        @endif
                    </div>
                </div>

                @if($reservation->special_requests)
                    <div class="mt-5 pt-5 border-t border-gray-100">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Special Requests</p>
                        <p class="text-sm text-gray-700">{{ $reservation->special_requests }}</p>
                    </div>
                @endif

                @if($reservation->notes)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Internal Notes</p>
                        <p class="text-sm text-gray-600">{{ $reservation->notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Payments card --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Payments</h3>
                    @if(!in_array($reservation->status, ['cancelled']) && $reservation->balance_due > 0)
                        <button onclick="document.getElementById('paymentModal').style.display='flex'"
                                class="text-sm text-hotel-600 hover:underline font-medium">
                            + Add Payment
                        </button>
                    @endif
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($reservation->payments as $payment)
                        <div class="flex items-center gap-4 px-6 py-3">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">${{ number_format($payment->amount, 2) }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ ucwords(str_replace('_', ' ', $payment->method)) }}
                                    · {{ $payment->paid_at?->format('M d, Y H:i') }}
                                </p>
                                @if($payment->notes)
                                    <p class="text-xs text-gray-400">{{ $payment->notes }}</p>
                                @endif
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                {{ $payment->type === 'refund' ? 'bg-purple-100 text-purple-700' : 'bg-green-100 text-green-700' }}">
                                {{ ucfirst($payment->type) }}
                            </span>
                        </div>
                    @empty
                        <div class="px-6 py-6 text-sm text-gray-400 text-center">No payments recorded</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Right: billing summary --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-4">Billing Summary</p>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">
                            {{ $reservation->nights }} nights × ${{ number_format($reservation->price_per_night, 2) }}
                        </dt>
                        <dd class="font-medium">
                            ${{ number_format($reservation->price_per_night * $reservation->nights, 2) }}
                        </dd>
                    </div>
                    @if($reservation->discount > 0)
                        <div class="flex justify-between text-green-600">
                            <dt>Discount</dt>
                            <dd>-${{ number_format($reservation->discount, 2) }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between pt-2 border-t border-gray-100 font-bold text-gray-900">
                        <dt>Total</dt>
                        <dd>${{ number_format($reservation->total_amount, 2) }}</dd>
                    </div>
                    <div class="flex justify-between text-green-600">
                        <dt>Paid</dt>
                        <dd>${{ number_format($reservation->paid_amount, 2) }}</dd>
                    </div>
                    @if($reservation->balance_due > 0)
                        <div class="flex justify-between font-semibold text-red-600 pt-2 border-t border-gray-100">
                            <dt>Balance Due</dt>
                            <dd>${{ number_format($reservation->balance_due, 2) }}</dd>
                        </div>
                    @else
                        <div class="flex justify-between font-semibold text-green-600 pt-2 border-t border-gray-100">
                            <dt>Fully Paid</dt>
                            <dd>✓</dd>
                        </div>
                    @endif
                </dl>
            </div>

            @if($reservation->payment_method)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 text-sm">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Payment Method</p>
                    <p class="text-gray-900">
                        {{ ucwords(str_replace('_', ' ', $reservation->payment_method)) }}
                    </p>
                </div>
            @endif
        </div>
    </div>

    {{-- Payment Modal --}}
    <div id="paymentModal"
         style="display:none; position:fixed; inset:0; z-index:50; align-items:center;
                justify-content:center; background:rgba(0,0,0,0.4);">
        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md mx-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">Add Payment</h3>
                <button onclick="document.getElementById('paymentModal').style.display='none'"
                        class="text-gray-400 hover:text-gray-600 text-lg leading-none">✕</button>
            </div>
            <form method="POST" action="{{ route('admin.reservations.payment', $reservation) }}"
                  class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Amount ($) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="amount" step="0.01" min="0.01"
                           value="{{ $reservation->balance_due }}" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Payment Method <span class="text-red-500">*</span>
                    </label>
                    <select name="method" required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400">
                        @foreach(\App\Models\Reservation::paymentMethods() as $method)
                            <option value="{{ $method }}">
                                {{ ucwords(str_replace('_', ' ', $method)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Transaction ID (optional)</label>
                    <input type="text" name="transaction_id"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <input type="text" name="notes"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="flex-1 bg-hotel-600 hover:bg-hotel-700 text-white text-sm font-medium py-2 rounded-lg transition-colors">
                        Record Payment
                    </button>
                    <button type="button"
                            onclick="document.getElementById('paymentModal').style.display='none'"
                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium py-2 rounded-lg transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
