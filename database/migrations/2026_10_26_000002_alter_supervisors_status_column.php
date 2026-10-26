<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Same reasoning as the users table: a string backed by an enum reads better
 * than a magic integer. Nothing writes to supervisors before this release.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supervisors', static function (Blueprint $table) {
            $table->string('status')->default('active')->change();
        });
    }

    public function down(): void
    {
        Schema::table('supervisors', static function (Blueprint $table) {
            $table->mediumInteger('status')->change();
        });
    }
};
