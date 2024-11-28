<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employee_insurances', static function (Blueprint $table) {
            $table->id();

            $table->foreignUuid('employee_id')->constrained('users')->cascadeOnDelete();

            $table->string('insurance_number');
            $table->string('insurance_company');
            $table->date('valid_from');
            $table->date('valid_to');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['active', 'expired', 'suspended'])->default('active');
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_insurances');
    }
};
