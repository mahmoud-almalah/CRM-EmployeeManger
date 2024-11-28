<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('meeting_attendees', static function (Blueprint $table) {
            $table->id();

            $table->foreignId('meeting_id')->constrained('meetings')->cascadeOnDelete();
            $table->uuidMorphs('attendee');

            $table->enum('status', ['accepted', 'declined', 'tentative', 'noreply'])->default('noreply');
            $table->text('note')->nullable();

            $table->dateTime('check_in_time')->nullable();
            $table->dateTime('check_out_time')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_attendees');
    }
};
