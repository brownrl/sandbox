<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all survey responses
        $responses = DB::table('survey_responses')->get();

        foreach ($responses as $response) {
            $questions = json_decode($response->questions, true);
            $updatedQuestions = [];

            // Map question IDs 21-40 to 1-20
            foreach ($questions as $questionId) {
                if ($questionId > 20) {
                    // Map duplicate questions (21-40) to originals (1-20)
                    $updatedQuestions[] = $questionId - 20;
                } else {
                    $updatedQuestions[] = $questionId;
                }
            }

            // Update the response with corrected question IDs
            DB::table('survey_responses')
                ->where('id', $response->id)
                ->update(['questions' => json_encode($updatedQuestions)]);
        }

        // Delete duplicate questions (21-40)
        DB::table('survey_questions')
            ->where('id', '>', 20)
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not easily reversible as we're deleting duplicate data
        // and mapping references. Manual database backup restoration would be needed.
    }
};
