<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('work_area_expenses', static function (Blueprint $table) {
            $table->id();

            $table->foreignId('work_area_id')->constrained('work_areas')->cascadeOnDelete();
            $table->string('expense_type');
            $table->decimal('amount', 10, 2);
            $table->date('expense_date');
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_area_expenses');
    }
};
