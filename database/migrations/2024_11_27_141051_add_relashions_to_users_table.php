<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', static function (Blueprint $table) {
            $table->dropColumn(['country_id', 'city_id', 'position_id', 'work_area_id', 'work_shift_id']);

            $table->foreignId('country_id')->nullable()->constrained('countries')->cascadeOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->cascadeOnDelete();
            $table->foreignId('position_id')->nullable()->constrained('positions')->cascadeOnDelete();
            $table->foreignId('work_area_id')->nullable()->constrained('work_areas')->cascadeOnDelete();
            $table->foreignId('work_shift_id')->nullable()->constrained('work_shifts')->cascadeOnDelete();
        });
    }
};
