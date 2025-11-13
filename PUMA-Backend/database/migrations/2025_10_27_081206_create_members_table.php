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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('cabinet_id')->constrained('cabinets')->onDelete('cascade');
            $table->foreignId('division_id')->constrained('divisions')->onDelete('cascade');
            $table->string('position'); // Head, Vice, Member, etc.
            $table->enum('status', ['active', 'inactive', 'alumni'])->default('active');
            $table->string('batch'); // 2023, 2024, etc.
            $table->date('joined_date')->nullable();
            $table->date('left_date')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'cabinet_id', 'division_id']);
            $table->index(['cabinet_id', 'division_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
