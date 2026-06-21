<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Raw SQL works on both MySQL and SQLite without needing doctrine/dbal.
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('ALTER TABLE assets MODIFY cover_path TEXT NULL');
        }
        // SQLite already stores TEXT regardless of declared type — no-op.
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('ALTER TABLE assets MODIFY cover_path VARCHAR(255) NULL');
        }
    }
};
