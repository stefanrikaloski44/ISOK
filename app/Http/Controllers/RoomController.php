<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('type', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%")
                    ->orWhere('room_number', 'like', "%{$request->search}%");
            });
        }
        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->status) {           // ← ADD THIS
            $query->where('status', $request->status);
        }

        $rooms = $query->orderBy('room_number')->paginate(12)->withQueryString();

        return view('rooms.index', compact('rooms'));
    }

    public function create()
    {
        return view('rooms.create', [
            'types' => Room::types(),
            'amenities' => Room::amenitiesList(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'room_number' => 'required|string|unique:rooms',
            'type' => 'required|in:' . implode(',', Room::types()),
            'description' => 'nullable|string',
            'price_per_night' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1|max:20',
            'floor' => 'required|integer|min:0',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string',
            'images' => 'nullable|array',   // remove |max:3 — enforced in storeImages()
            'images.*' => 'nullable|image|max:2048',
        ]);

        $data['status'] = 'available';
        $data['images'] = $this->storeImages($request);

        Room::create($data);

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Room created successfully!');
    }

    public function show(Room $room)
    {
        $upcomingReservations = $room->reservations()
            ->whereIn('status', ['pending', 'confirmed'])
            ->with('guest')
            ->orderBy('check_in_date')
            ->get();

        $recentHistory = $room->reservations()
            ->whereIn('status', ['checked_out', 'cancelled'])
            ->with('guest')
            ->orderBy('check_out_date', 'desc')
            ->take(10)
            ->get();

        return view('rooms.show', compact('room', 'upcomingReservations', 'recentHistory'));
    }

    public function edit(Room $room)
    {
        return view('rooms.edit', [
            'room' => $room,
            'types' => Room::types(),
            'amenities' => Room::amenitiesList(),
        ]);
    }

    public function update(Request $request, Room $room)
    {
        $data = $request->validate([
            'room_number' => 'required|string|unique:rooms,room_number,' . $room->id,
            'type' => 'required|in:' . implode(',', Room::types()),
            'description' => 'nullable|string',
            'price_per_night' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1|max:20',
            'floor' => 'required|integer|min:0',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string',
            'new_images' => 'nullable|array',
            'new_images.*' => 'image|max:2048',
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'string',
        ]);

        // Start from existing images
        $existingImages = $room->images ?? [];

        // Remove images the admin checked for deletion
        if (!empty($data['remove_images'])) {
            foreach ($data['remove_images'] as $path) {
                Storage::disk('public')->delete($path);
                $existingImages = array_values(array_filter(
                    $existingImages,
                    fn($img) => $img !== $path
                ));
            }
        }

        // Add newly uploaded images (respect the 3-image cap)
        if ($request->hasFile('new_images')) {
            $slotsLeft = 3 - count($existingImages);
            foreach (array_slice($request->file('new_images'), 0, $slotsLeft) as $file) {
                $existingImages[] = $file->store('rooms', 'public');
            }
        }

        $data['images'] = array_values($existingImages);
        unset($data['new_images'], $data['remove_images']);

        $room->update($data);

        return redirect()->route('admin.rooms.show', $room)
            ->with('success', 'Room updated successfully!');
    }

    public function destroy(Room $room)
    {
        if ($room->reservations()->whereIn('status', ['confirmed', 'checked_in'])->exists()) {
            return back()->with('error', 'Cannot delete a room with active reservations.');
        }

        // Delete all stored images
        foreach ($room->images ?? [] as $path) {
            Storage::disk('public')->delete($path);
        }

        $room->delete();

        return redirect()->route('admin.rooms.index')
            ->with('success', 'Room deleted successfully!');
    }

    // ── Private helpers ────────────────────────────────────────────────────────

    private function storeImages(Request $request): array
    {
        if (!$request->hasFile('images')) {
            return [];
        }

        $paths = [];
        foreach (array_slice($request->file('images'), 0, 3) as $file) {
            // Skip any slot that didn't get a real file (null entries)
            if ($file && $file->isValid()) {
                $paths[] = $file->store('rooms', 'public');
            }
        }

        return $paths;
    }
}
