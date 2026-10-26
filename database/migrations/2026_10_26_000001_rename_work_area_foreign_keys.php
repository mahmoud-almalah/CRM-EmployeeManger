<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The original migration named the foreign keys `city` and `country`; every
 * other table uses `<entity>_id`. The original migration is left untouched
 * because it has already run elsewhere.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_areas', static function (Blueprint $table) {
            $table->dropForeign(['city']);
            $table->dropForeign(['country']);
        });

        Schema::table('work_areas', static function (Blueprint $table) {
            $table->renameColumn('city', 'city_id');
            $table->renameColumn('country', 'country_id');
        });

        Schema::table('work_areas', static function (Blueprint $table) {
            $table->foreign('city_id')->references('id')->on('cities')->cascadeOnDelete();
            $table->foreign('country_id')->references('id')->on('countries')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('work_areas', static function (Blueprint $table) {
            $table->dropForeign(['city_id']);
            $table->dropForeign(['country_id']);
        });

        Schema::table('work_areas', static function (Blueprint $table) {
            $table->renameColumn('city_id', 'city');
            $table->renameColumn('country_id', 'country');
        });

        Schema::table('work_areas', static function (Blueprint $table) {
            $table->foreign('city')->references('id')->on('cities')->cascadeOnDelete();
            $table->foreign('country')->references('id')->on('countries')->cascadeOnDelete();
        });
    }
};
