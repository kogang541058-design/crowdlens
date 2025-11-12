<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('disaster_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default disaster types
        DB::table('disaster_types')->insert([
            ['name' => 'Flood', 'icon' => '🌊', 'color' => '#3b82f6', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Landslide', 'icon' => '⛰️', 'color' => '#92400e', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disaster_types');
    }
};
