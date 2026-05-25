@extends('layouts.app')
@section('page-title', 'Edit ' . ucfirst($room->type) . ' Room')
@section('breadcrumbs')
    <a href="{{ route('admin.rooms.index') }}" class="hover:text-gray-600">Rooms</a> /
    <a href="{{ route('admin.rooms.show', $room) }}" class="hover:text-gray-600">{{ ucfirst($room->type) }}</a> / Edit
@endsection

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <form method="POST" action="{{ route('admin.rooms.update', $room) }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                {{-- ── Photos section ── --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Room Photos
                        <span class="text-gray-400 font-normal">(up to 3 total)</span>
                    </label>

                    @php
                        $existingImages = $room->images ?? [];
                        $existingUrls   = $room->image_urls;
                        $freeSlots      = 3 - count($existingImages);
                    @endphp

                    {{-- Existing photos with remove checkbox --}}
                    @if(count($existingImages) > 0)
                        <div class="mb-3">
                            <p class="text-xs text-gray-400 mb-2">Current photos — check to remove</p>
                            <div class="grid grid-cols-3 gap-3">
                                @foreach($existingImages as $i => $path)
                                    <div class="relative group">
                                        <div class="aspect-square rounded-xl overflow-hidden border border-gray-100">
                                            <img src="{{ $existingUrls[$i] }}" alt="Room photo {{ $i + 1 }}"
                                                 class="w-full h-full object-cover">
                                        </div>
                                        {{-- Remove toggle --}}
                                        <label class="absolute inset-0 rounded-xl cursor-pointer flex items-center justify-center
                                          bg-black bg-opacity-0 hover:bg-opacity-40 transition-all group/lbl"
                                               title="Check to remove this photo">
                                            <input type="checkbox" name="remove_images[]" value="{{ $path }}"
                                                   class="hidden peer"
                                                   onchange="toggleRemove(this, {{ $i }})">
                                            <div id="removeBadge-{{ $i }}"
                                                 class="hidden peer-checked:flex w-8 h-8 rounded-full bg-red-500 text-white
                                            items-center justify-center text-sm font-bold shadow-lg">✕</div>
                                        </label>
                                        {{-- Visual overlay when marked for removal --}}
                                        <div id="removeOverlay-{{ $i }}"
                                             class="hidden absolute inset-0 rounded-xl bg-red-500 bg-opacity-30
                                        border-2 border-red-500 pointer-events-none"></div>
                                        @if($i === 0)
                                            <p class="text-center text-xs text-gray-400 mt-1">Cover</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- New image upload slots (only if under 3) --}}
                    @if($freeSlots > 0)
                        <div>
                            @if(count($existingImages) > 0)
                                <p class="text-xs text-gray-400 mb-2">Add more photos ({{ $freeSlots }} slot{{ $freeSlots > 1 ? 's' : '' }} available)</p>
                            @endif
                            <div class="grid grid-cols-3 gap-3">
                                @for($i = 0; $i < $freeSlots; $i++)
                                    <div class="relative group" id="newSlot-{{ $i }}">
                                        <input type="file" name="new_images[]" accept="image/*"
                                               id="newFileInput-{{ $i }}" class="hidden"
                                               onchange="previewNewSlot(this, {{ $i }})">

                                        <button type="button" id="newSlotBtn-{{ $i }}"
                                                onclick="document.getElementById('newFileInput-{{ $i }}').click()"
                                                class="w-full aspect-square rounded-xl border-2 border-dashed border-gray-200
                                           hover:border-hotel-400 transition-colors flex flex-col items-center
                                           justify-center gap-1 text-gray-300 hover:text-hotel-400">
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            <span class="text-xs">Add photo</span>
                                        </button>

                                        <div id="newSlotPreview-{{ $i }}" class="hidden relative aspect-square rounded-xl overflow-hidden">
                                            <img id="newSlotImg-{{ $i }}" src="" alt="" class="w-full h-full object-cover">
                                            <button type="button" onclick="clearNewSlot({{ $i }})"
                                                    class="absolute top-1.5 right-1.5 w-6 h-6 bg-black bg-opacity-60
                                               hover:bg-opacity-80 text-white rounded-full flex items-center
                                               justify-center text-xs leading-none">✕</button>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    @else
                        <p class="text-xs text-amber-600 bg-amber-50 border border-amber-200 px-3 py-2 rounded-lg">
                            Maximum of 3 photos reached. Remove an existing photo to add a new one.
                        </p>
                    @endif

                    @error('new_images')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    @error('new_images.*')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- ── Room fields ── --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Room Type <span class="text-red-500">*</span></label>
                        <select name="type" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400">
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ old('type', $room->type) == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Room Number <span class="text-red-500">*</span></label>
                        <input type="text" name="room_number" value="{{ old('room_number', $room->room_number) }}" required
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400 @error('room_number') border-red-400 @enderror">
                        @error('room_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Price per Night ($) <span class="text-red-500">*</span></label>
                        <input type="number" name="price_per_night" value="{{ old('price_per_night', $room->price_per_night) }}" step="0.01" min="0" required
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400">
                        @error('price_per_night')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Max Capacity <span class="text-red-500">*</span></label>
                        <input type="number" name="capacity" value="{{ old('capacity', $room->capacity) }}" min="1" max="20" required
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Floor <span class="text-red-500">*</span></label>
                    <input type="number" name="floor" value="{{ old('floor', $room->floor) }}" min="0" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400">{{ old('description', $room->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amenities</label>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach($amenities as $amenity)
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="{{ $amenity }}"
                                       {{ in_array($amenity, old('amenities', $room->amenities ?? [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-hotel-600 rounded border-gray-300 focus:ring-hotel-400">
                                {{ $amenity }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                    <button type="submit" class="bg-hotel-600 hover:bg-hotel-700 text-white text-sm font-medium px-6 py-2 rounded-lg transition-colors">
                        Update Room
                    </button>
                    <a href="{{ route('admin.rooms.show', $room) }}" class="text-gray-500 hover:text-gray-700 text-sm">Cancel</a>

                    <form method="POST" action="{{ route('admin.rooms.destroy', $room) }}" class="ml-auto"
                          onsubmit="return confirm('Delete this room? This cannot be undone.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium">Delete Room</button>
                    </form>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // New slot preview
            function previewNewSlot(input, index) {
                if (!input.files || !input.files[0]) return;
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('newSlotBtn-' + index).classList.add('hidden');
                    document.getElementById('newSlotPreview-' + index).classList.remove('hidden');
                    document.getElementById('newSlotImg-' + index).src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }

            function clearNewSlot(index) {
                document.getElementById('newFileInput-' + index).value = '';
                document.getElementById('newSlotPreview-' + index).classList.add('hidden');
                document.getElementById('newSlotBtn-' + index).classList.remove('hidden');
                document.getElementById('newSlotImg-' + index).src = '';
            }

            // Existing photo removal toggle
            function toggleRemove(checkbox, index) {
                const overlay = document.getElementById('removeOverlay-' + index);
                const badge   = document.getElementById('removeBadge-' + index);
                if (checkbox.checked) {
                    overlay.classList.remove('hidden');
                    badge.classList.remove('hidden');
                } else {
                    overlay.classList.add('hidden');
                    badge.classList.add('hidden');
                }
            }
        </script>
    @endpush
@endsection
