<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_areas', static function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('address');
            $table->foreignId('city')->constrained('cities')->cascadeOnDelete();
            $table->foreignId('country')->constrained('countries')->cascadeOnDelete();
            $table->foreignUuid('manager_id')->constrained('users')->cascadeOnDelete();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->enum('status', ['open', 'closed', 'under_maintenance'])->default('open');
            $table->string('opening_hours')->nullable();
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_areas');
    }
};
