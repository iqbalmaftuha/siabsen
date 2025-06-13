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
        Schema::create('konfigloks', function (Blueprint $table) {
            $table->id();
            $table->string('lokasi_kantor'); // Format: "latitude,longitude"
            $table->unsignedInteger('radius'); // Dalam meter
            $table->time('jam_masuk_standar')->nullable(); // Jam masuk standar kantor
            $table->time('jam_pulang_standar')->nullable(); // Jam pulang standar kantor
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konfigloks');
    }
};
