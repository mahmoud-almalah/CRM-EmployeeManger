<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
 | Users have UUID primary keys, but the Passport tables published with the
 | original skeleton store user_id as an unsigned bigint. SQLite accepts a
 | UUID in that column, MySQL rejects it ("Data truncated for column
 | 'user_id'"), so signing in failed with a 500 on MySQL only.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('oauth_access_tokens', function (Blueprint $table): void {
            $table->uuid('user_id')->nullable()->change();
        });

        Schema::table('oauth_auth_codes', function (Blueprint $table): void {
            $table->uuid('user_id')->change();
        });

        Schema::table('oauth_clients', function (Blueprint $table): void {
            $table->uuid('user_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Tokens that point at UUID users cannot survive the narrower type.
        DB::table('oauth_access_tokens')->delete();
        DB::table('oauth_auth_codes')->delete();
        DB::table('oauth_clients')->whereNotNull('user_id')->update(['user_id' => null]);

        Schema::table('oauth_access_tokens', function (Blueprint $table): void {
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });

        Schema::table('oauth_auth_codes', function (Blueprint $table): void {
            $table->unsignedBigInteger('user_id')->change();
        });

        Schema::table('oauth_clients', function (Blueprint $table): void {
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }
};
