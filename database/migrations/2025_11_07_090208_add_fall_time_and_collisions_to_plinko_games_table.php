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
        Schema::table('plinko_games', function (Blueprint $table) {
            $table->integer('fall_time_ms')->after('horizontal_distance')->nullable();
            $table->integer('peg_collisions')->after('fall_time_ms')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plinko_games', function (Blueprint $table) {
            $table->dropColumn(['fall_time_ms', 'peg_collisions']);
        });
    }
};