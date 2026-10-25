<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', static function (Blueprint $table) {
            $table->uuid('id')->primary()->index();

            // Name fields
            $table->string('name');
            $table->string('username')->unique();

            // Contact fields
            $table->string('email')->unique();
            $table->string('phone_number')->unique();

            // Address fields
            $table->string('address')->nullable();
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('city_id');

            // Date fields
            $table->date('birth_date');
            $table->date('start_working_date');
            $table->date('end_working_date')->nullable();
            $table->string('termination_reason')->nullable();

            // Work fields
            $table->unsignedBigInteger('position_id');
            $table->unsignedBigInteger('work_area_id');
            $table->unsignedBigInteger('work_shift_id');

            // Salary fields
            $table->decimal('salary', 10, 2);
            $table->string('salary_currency')->default('USD');

            // Check fields
            $table->boolean('is_freelance')->default(false);
            $table->boolean('is_remote')->default(false);

            // Enum fields
            $table->mediumInteger('status');
            $table->mediumInteger('role');

            // Image fields
            $table->string('image')->nullable();

            // Password fields
            $table->string('password');
            $table->timestamp('password_changed_at')->nullable();

            // Token fields
            $table->rememberToken();

            // Other
            $table->json('additional_info')->nullable();

            // Timestamp fields
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', static function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', static function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignUuid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
