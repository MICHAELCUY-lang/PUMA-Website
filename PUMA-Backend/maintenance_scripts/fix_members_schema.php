<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Recreating members table to remove unique constraint...\n";

try {
    // 0. Cleanup from previous failed runs
    Schema::dropIfExists('members_backup_fix');

    // 1. Rename current table
    Schema::rename('members', 'members_backup_fix');
    
    // 2. Create new table WITHOUT unique constraint
    Schema::create('members', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
        $table->foreignId('cabinet_id')->nullable()->constrained('cabinets')->onDelete('cascade');
        $table->foreignId('division_id')->nullable()->constrained('divisions')->onDelete('cascade');
        $table->string('name');
        $table->string('position')->nullable();
        $table->string('photo_path')->nullable();
        $table->string('email')->nullable(); // Making email nullable just in case
        $table->string('instagram_url')->nullable();
        $table->string('linkedin_url')->nullable();
        $table->string('status')->default('active'); // active, alumni
        $table->string('batch')->nullable(); // e.g., "2023/2024"
        $table->date('birthdate')->nullable();
        $table->date('joined_date')->nullable();
        $table->date('left_date')->nullable();
        $table->integer('display_order')->default(0);
        $table->boolean('is_visible')->default(true);
        $table->timestamps();
        
        // NO UNIQUE CONSTRAINT HERE!
    });
    
    // 3. Copy data
    // We need to list columns explicitly to avoid mismatch if schema changed slightly
    DB::statement('INSERT INTO members (id, user_id, cabinet_id, division_id, name, position, photo_path, email, instagram_url, linkedin_url, status, batch, birthdate, joined_date, left_date, display_order, is_visible, created_at, updated_at) 
                   SELECT id, user_id, cabinet_id, division_id, name, position, photo_path, email, instagram_url, linkedin_url, status, batch, birthdate, joined_date, left_date, display_order, is_visible, created_at, updated_at FROM members_backup_fix');
    
    // 4. Drop backup
    Schema::drop('members_backup_fix');
    
    echo "SUCCESS: Members table recreated without unique constraint.\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    // Attempt rollback
    if (Schema::hasTable('members_backup_fix') && !Schema::hasTable('members')) {
        Schema::rename('members_backup_fix', 'members');
        echo "Rolled back table rename.\n";
    }
}
