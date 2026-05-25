<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Add new images column (JSON array of paths)
            $table->json('images')->nullable()->after('image');
        });

        // Migrate existing single image into the new array column
        DB::table('rooms')->whereNotNull('image')->get()->each(function ($room) {
            DB::table('rooms')->where('id', $room->id)->update([
                'images' => json_encode([$room->image]),
            ]);
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('image')->nullable()->after('images');
        });

        // Migrate back: take first image from array
        DB::table('rooms')->whereNotNull('images')->get()->each(function ($room) {
            $images = json_decode($room->images, true);
            DB::table('rooms')->where('id', $room->id)->update([
                'image' => $images[0] ?? null,
            ]);
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('images');
        });
    }
};
