<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // In this implementation, we're storing character slugs directly in the 
        // survey_responses.character column, which allows for flexibility while
        // still maintaining reference to our StarWarsCharacter model.
        // 
        // The character field contains slugs that correspond to entries in the
        // star_wars_characters table, which is validated on the application level.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
