<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities', static function (Blueprint $table) {
            $table->id();

            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();

            $table->string('name');
            $table->string('code')->nullable();
            $table->json('coordinates')->nullable();
            $table->string('timezone')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
