<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('meetings', static function (Blueprint $table) {
            $table->id();
            $table->uuidMorphs('created_by');

            $table->string('title');
            $table->text('description')->nullable();

            $table->enum('meeting_type', ['physical', 'virtual']);
            $table->string('link')->nullable();
            $table->string('location')->nullable();

            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->enum('type', ['required', 'optional'])->default('optional');
            $table->enum('priority', ['high', 'medium', 'low']);

            $table->dateTime('meeting_date');
            $table->dateTime('start_time');
            $table->dateTime('end_time');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
