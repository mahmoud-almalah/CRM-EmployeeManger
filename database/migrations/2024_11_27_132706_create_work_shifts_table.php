<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('work_shifts', static function (Blueprint $table) {
            $table->id();

            $table->foreignId('work_area_id')->constrained('work_areas')->cascadeOnDelete();

            $table->enum('name', ['morning', 'afternoon', 'night']);
            $table->time('start_time');
            $table->time('end_time');
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_shifts');
    }
};
