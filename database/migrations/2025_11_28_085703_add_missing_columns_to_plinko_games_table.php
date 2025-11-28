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
            $table->decimal('drop_x', 8, 2)->after('final_slot')->nullable();
            $table->decimal('final_x', 8, 2)->after('drop_x')->nullable();
            $table->decimal('horizontal_distance', 8, 2)->after('final_x')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plinko_games', function (Blueprint $table) {
            $table->dropColumn(['drop_x', 'final_x', 'horizontal_distance']);
        });
    }
};
