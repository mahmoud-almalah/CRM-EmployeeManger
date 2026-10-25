<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_records', static function (Blueprint $table) {
            $table->id();

            $table->foreignUuid('employee_id')->constrained('users')->cascadeOnDelete();

            $table->date('date');
            $table->enum('status', ['present', 'absent', 'on_leave', 'holiday', 'weekend'])->default('present');
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->decimal('hours_worked', 5, 2)->nullable();
            $table->decimal('overtime_hours', 5, 2)->default(0);
            $table->text('leave_reason')->nullable();
            $table->boolean('is_paid_leave')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
