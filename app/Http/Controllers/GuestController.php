<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function create()
    {
        return view('admin.guests.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'email'         => 'required|email|unique:guests',
            'phone'         => 'nullable|string|max:30',
            'id_number'     => 'nullable|string|max:50',
            'nationality'   => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date|before:today',
            'address'       => 'nullable|string',
            'city'          => 'nullable|string|max:100',
            'country'       => 'nullable|string|max:100',
            'notes'         => 'nullable|string',
        ]);

        $guest = Guest::create($data);

        return redirect()->route('admin.guests.show', $guest)
            ->with('success', 'Guest created successfully!');
    }


    public function edit(Guest $guest)
    {
        return view('admin.guests.edit', compact('guest'));
    }

    public function update(Request $request, Guest $guest)
    {
        $data = $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'email'         => 'required|email|unique:guests,email,' . $guest->id,
            'phone'         => 'nullable|string|max:30',
            'id_number'     => 'nullable|string|max:50',
            'nationality'   => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date|before:today',
            'address'       => 'nullable|string',
            'city'          => 'nullable|string|max:100',
            'country'       => 'nullable|string|max:100',
            'notes'         => 'nullable|string',
        ]);

        $guest->update($data);

        return redirect()->route('admin.guests.show', $guest)
            ->with('success', 'Guest updated successfully!');
    }

    public function destroy(Guest $guest)
    {
        if ($guest->reservations()->whereIn('status', ['confirmed', 'checked_in'])->exists()) {
            return back()->with('error', 'Cannot delete a guest with active reservations.');
        }
        $guest->delete();
        return redirect()->route('admin.guests.index')
            ->with('success', 'Guest deleted successfully!');
    }

    public function search(Request $request)
    {
        $guests = Guest::where('first_name', 'like', "%{$request->q}%")
            ->orWhere('last_name', 'like', "%{$request->q}%")
            ->orWhere('email', 'like', "%{$request->q}%")
            ->limit(10)
            ->get(['id', 'first_name', 'last_name', 'email', 'phone']);

        return response()->json($guests);
    }
}
