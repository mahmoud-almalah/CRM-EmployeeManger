<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supervisors', static function (Blueprint $table) {
            $table->uuid('id')->primary()->index();

            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone_number')->unique();
            $table->mediumInteger('status');
            $table->string('password');
            $table->string('profile_picture')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supervisors');
    }
};
