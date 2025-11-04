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
        Schema::create('plinko_games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('score');
            $table->integer('drop_position'); // 0-8 for which slot the chip was dropped from
            $table->integer('final_slot'); // 0-8 for which slot the chip landed in
            $table->json('path')->nullable(); // Store the path the chip took for replay
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plinko_games');
    }
};
