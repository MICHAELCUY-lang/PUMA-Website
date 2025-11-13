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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->datetime('event_date');
            $table->datetime('event_date_end')->nullable();
            $table->string('location')->nullable();
            $table->foreignId('cabinet_id')->nullable()->constrained('cabinets')->onDelete('cascade');
            $table->enum('status', ['completed', 'upcoming', 'cancelled'])->default('upcoming');
            $table->text('content')->nullable(); // Full article content
            $table->string('category')->nullable(); // e.g., 'recruitment', 'training', 'competition'
            $table->integer('views')->default(0);
            $table->timestamps();
            $table->index(['cabinet_id', 'status', 'event_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
