<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('meeting_requests', static function (Blueprint $table) {
            $table->id();

            $table->uuidMorphs('requested_by');
            $table->uuidMorphs('requested_to');

            $table->string('title');
            $table->text('description')->nullable();

            $table->enum('meeting_type', ['physical', 'virtual']);
            $table->enum('status', ['accepted', 'declined', 'pending']);
            $table->enum('priority', ['high', 'medium', 'low']);

            $table->dateTime('requested_date')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('meeting_requests');
    }
};
