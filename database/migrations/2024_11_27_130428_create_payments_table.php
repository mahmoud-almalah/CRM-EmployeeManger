<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', static function (Blueprint $table) {
            $table->id();

            $table->foreignUuid('employee_id')->constrained('users')->cascadeOnDelete();

            $table->date('payment_date');
            $table->enum('type', ['salary', 'bonus', 'deduction']);
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('USD');
            $table->text('description')->nullable();
            $table->boolean('is_bonus')->default(false);
            $table->boolean('is_deduction')->default(false);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
