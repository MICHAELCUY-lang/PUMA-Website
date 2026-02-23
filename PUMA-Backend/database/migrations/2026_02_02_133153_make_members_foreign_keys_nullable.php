<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to modify columns
        DB::statement('ALTER TABLE members MODIFY COLUMN user_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE members MODIFY COLUMN cabinet_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE members MODIFY COLUMN division_id BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to NOT NULL
        DB::statement('ALTER TABLE members MODIFY COLUMN user_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE members MODIFY COLUMN cabinet_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE members MODIFY COLUMN division_id BIGINT UNSIGNED NOT NULL');
    }
};
