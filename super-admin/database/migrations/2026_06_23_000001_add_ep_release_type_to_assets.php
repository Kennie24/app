<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('assets', 'release_type')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE assets MODIFY release_type ENUM('single', 'ep', 'album') NOT NULL DEFAULT 'single'");
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('assets', 'release_type')) {
            return;
        }

        DB::table('assets')->where('release_type', 'ep')->update(['release_type' => 'album']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE assets MODIFY release_type ENUM('single', 'album') NOT NULL DEFAULT 'single'");
        }
    }
};
