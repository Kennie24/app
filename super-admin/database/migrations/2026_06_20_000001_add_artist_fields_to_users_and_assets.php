<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_artist')->default(false)->after('is_super_admin');
            $table->string('artist_name')->nullable()->after('is_artist');
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->enum('release_type', ['single', 'ep', 'album'])->default('single')->after('artist');
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn('release_type');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_artist', 'artist_name']);
        });
    }
};
