@extends('layouts.app')
@section('page-title', 'Reservations')

@section('content')
    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search by number, guest name..."
               class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400 w-64">
        <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400">
            <option value="">All Statuses</option>
            @foreach(\App\Models\Reservation::statuses() as $status)
                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                    {{ ucwords(str_replace('_', ' ', $status)) }}
                </option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}"
               class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400">
        <input type="date" name="date_to" value="{{ request('date_to') }}"
               class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400">
        <button type="submit"
                class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg">
            Filter
        </button>
        @if(request()->hasAny(['search','status','date_from','date_to']))
            <a href="{{ route('admin.reservations.index') }}"
               class="bg-white border border-gray-200 text-gray-500 text-sm px-4 py-2 rounded-lg hover:bg-gray-50">
                Clear
            </a>
        @endif
    </form>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Reservation</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Guest</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Room</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Dates</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Amount</th>
                <th class="px-4 py-3"></th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
            @forelse($reservations as $res)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-3">
                        <p class="font-mono text-xs font-medium text-hotel-700">{{ $res->reservation_number }}</p>
                        <p class="text-xs text-gray-400">{{ $res->created_at->format('M d, Y') }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-900">{{ $res->guest->full_name }}</p>
                        <p class="text-xs text-gray-400">{{ $res->guests_count }} guest(s)</p>
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.rooms.show', $res->room) }}"
                           class="font-medium text-gray-900 hover:text-hotel-700">
                            {{ $res->room->room_number }}
                        </a>
                        <p class="text-xs text-gray-400 capitalize">{{ $res->room->type }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <p class="text-gray-900">{{ $res->check_in_date->format('M d') }} → {{ $res->check_out_date->format('M d') }}</p>
                        <p class="text-xs text-gray-400">{{ $res->nights }} night(s)</p>
                    </td>
                    <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $res->status_badge }}">
                                {{ ucwords(str_replace('_', ' ', $res->status)) }}
                            </span>
                        <br>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs mt-1 {{ $res->payment_status_badge }}">
                                {{ ucfirst($res->payment_status) }}
                            </span>
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-semibold text-gray-900">${{ number_format($res->total_amount, 0) }}</p>
                        @if($res->balance_due > 0)
                            <p class="text-xs text-red-500">Due: ${{ number_format($res->balance_due, 0) }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.reservations.show', $res) }}"
                           class="text-xs text-hotel-600 hover:underline">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                        No reservations found
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $reservations->links() }}</div>
@endsection
