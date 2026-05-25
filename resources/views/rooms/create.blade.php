@extends('layouts.app')
@section('page-title', 'Add Room')
@section('breadcrumbs')
    <a href="{{ route('admin.rooms.index') }}" class="hover:text-gray-600">Rooms</a> / Add Room
@endsection

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <form method="POST" action="{{ route('admin.rooms.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                {{-- ── Multi-image upload ── --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Room Photos
                        <span class="text-gray-400 font-normal">(up to 3 · JPG or PNG · max 2 MB each)</span>
                    </label>

                    <div class="grid grid-cols-3 gap-3">
                        @for($i = 0; $i < 3; $i++)
                            <div class="relative group">
                                <input type="file" name="images[]" accept="image/*"
                                       id="fileInput-{{ $i }}" style="display:none"
                                       onchange="previewSlot(this, {{ $i }})">

                                <button type="button" id="slotBtn-{{ $i }}"
                                        onclick="document.getElementById('fileInput-{{ $i }}').click()"
                                        class="w-full rounded-xl border-2 border-dashed border-gray-200
                           hover:border-hotel-400 transition-colors flex flex-col
                           items-center justify-center gap-1 text-gray-300 hover:text-hotel-400"
                                        style="height:160px;">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    <span class="text-xs">Photo {{ $i + 1 }}</span>
                                </button>

                                <div id="slotPreview-{{ $i }}" class="relative rounded-xl overflow-hidden"
                                     style="display:none; height:160px;">
                                    <img id="slotImg-{{ $i }}" src="" alt=""
                                         style="width:100%; height:100%; object-fit:cover; display:block;">
                                    <button type="button" onclick="clearSlot({{ $i }})"
                                            style="position:absolute; top:6px; right:6px; width:24px; height:24px;
                               background:rgba(0,0,0,0.6); color:#fff; border-radius:50%;
                               border:none; cursor:pointer; font-size:12px; display:flex;
                               align-items:center; justify-content:center;">✕</button>
                                </div>

                                <div id="slotInfo-{{ $i }}"
                                     style="display:none; margin-top:6px; padding:6px 8px; background:#f0fdf4;
                        border:1px solid #bbf7d0; border-radius:8px; align-items:center; gap:6px;">
                                    <svg style="width:14px;height:14px;flex-shrink:0;" fill="none" stroke="#16a34a" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span id="slotFileName-{{ $i }}"
                                          style="font-size:11px; color:#15803d; font-weight:500;
                             white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
                             flex:1; min-width:0;"></span>
                                    <span id="slotFileSize-{{ $i }}"
                                          style="font-size:11px; color:#16a34a; flex-shrink:0;"></span>
                                </div>

                                @if($i === 0)
                                    <p style="text-align:center; font-size:11px; color:#9ca3af; margin-top:4px;">Cover photo</p>
                                @endif
                            </div>
                        @endfor
                    </div>

                    @error('images')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    @error('images.*')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- ── Room fields ── --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Room Type <span class="text-red-500">*</span></label>
                        <select name="type" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400">
                            <option value="">Select type</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                        @error('type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Room Number <span class="text-red-500">*</span></label>
                        <input type="text" name="room_number" value="{{ old('room_number') }}" required
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400 @error('room_number') border-red-400 @enderror"
                               placeholder="e.g. 101, 205A">
                        @error('room_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Price per Night ($) <span class="text-red-500">*</span></label>
                        <input type="number" name="price_per_night" value="{{ old('price_per_night') }}" step="0.01" min="0" required
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400"
                               placeholder="150.00">
                        @error('price_per_night')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Max Capacity <span class="text-red-500">*</span></label>
                        <input type="number" name="capacity" value="{{ old('capacity', 2) }}" min="1" max="20" required
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Floor <span class="text-red-500">*</span></label>
                    <input type="number" name="floor" value="{{ old('floor', 1) }}" min="0" required
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-hotel-400"
                              placeholder="Brief description of the room...">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amenities</label>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach($amenities as $amenity)
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="{{ $amenity }}"
                                       {{ in_array($amenity, old('amenities', [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-hotel-600 rounded border-gray-300 focus:ring-hotel-400">
                                {{ $amenity }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                    <button type="submit" class="bg-hotel-600 hover:bg-hotel-700 text-white text-sm font-medium px-6 py-2 rounded-lg transition-colors">
                        Create Room
                    </button>
                    <a href="{{ route('admin.rooms.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function fmt(b) {
                return b < 1048576 ? (b/1024).toFixed(1)+' KB' : (b/1048576).toFixed(1)+' MB';
            }
            function previewSlot(input, idx) {
                var file = input.files[0];
                if (!file) return;
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('slotBtn-'+idx).style.display     = 'none';
                    document.getElementById('slotPreview-'+idx).style.display = 'block';
                    document.getElementById('slotImg-'+idx).src               = e.target.result;
                    document.getElementById('slotInfo-'+idx).style.display    = 'flex';
                    document.getElementById('slotFileName-'+idx).textContent  = file.name;
                    document.getElementById('slotFileSize-'+idx).textContent  = fmt(file.size);
                };
                reader.readAsDataURL(file);
            }
            function clearSlot(idx) {
                document.getElementById('fileInput-'+idx).value              = '';
                document.getElementById('slotPreview-'+idx).style.display    = 'none';
                document.getElementById('slotBtn-'+idx).style.display        = '';
                document.getElementById('slotImg-'+idx).src                  = '';
                document.getElementById('slotInfo-'+idx).style.display       = 'none';
            }
        </script>
    @endpush
@endsection
