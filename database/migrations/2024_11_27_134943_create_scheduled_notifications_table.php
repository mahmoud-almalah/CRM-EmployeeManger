<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scheduled_notifications', static function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('message');

            $table->enum('type', ['email', 'sms', 'push'])->default('email');
            $table->enum('status', ['important', 'normal', 'low'])->default('normal');
            $table->enum('every', ['day', 'week', 'month', 'year'])->default('day');
            $table->integer('interval')->default(1);

            $table->json('data')->nullable();

            $table->timestamp('send_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheduled_notifications');
    }
};
