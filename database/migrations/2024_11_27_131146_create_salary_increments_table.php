<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('salary_increments', static function (Blueprint $table) {
            $table->id();

            $table->foreignUuid('employee_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('hr_id')->constrained('users')->cascadeOnDelete();

            $table->decimal('previous_salary', 10, 2);
            $table->decimal('new_salary', 10, 2);
            $table->decimal('increase_amount', 10, 2);
            $table->text('reason')->nullable();
            $table->date('effective_date');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_increments');
    }
};
