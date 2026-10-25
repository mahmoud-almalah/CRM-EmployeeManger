<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', static function (Blueprint $table) {
            $table->id();

            $table->uuidMorphs('sender');
            $table->uuidMorphs('receiver');

            $table->string('title');
            $table->text('message');

            $table->enum('type', ['email', 'sms', 'push'])->default('email');
            $table->enum('status', ['important', 'normal', 'low'])->default('normal');

            $table->json('data')->nullable();

            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
