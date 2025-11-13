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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('video_url'); // YouTube, Vimeo, or direct link
            $table->string('thumbnail_url')->nullable();
            $table->foreignId('cabinet_id')->constrained('cabinets')->onDelete('cascade');
            $table->string('batch'); // For which batch is this video
            $table->integer('order')->default(0);
            $table->enum('access_level', ['public', 'registered', 'members_only'])->default('registered');
            $table->integer('views')->default(0);
            $table->timestamps();
            $table->index(['cabinet_id', 'batch', 'access_level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
