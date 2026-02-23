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
        Schema::table('members', function (Blueprint $table) {
            $table->string('name')->after('id')->nullable();
            $table->string('email')->after('name')->nullable();
        });
        
        // Populate existing rows if any
        $members = \Illuminate\Support\Facades\DB::table('members')->get();
        foreach($members as $member) {
             $user = \Illuminate\Support\Facades\DB::table('users')->where('id', $member->user_id)->first();
             if ($user) {
                 \Illuminate\Support\Facades\DB::table('members')
                    ->where('id', $member->id)
                    ->update([
                        'name' => $user->name,
                        'email' => $user->email
                    ]);
             }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['name', 'email']);
        });
    }
};
