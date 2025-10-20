<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { character_statistics } from '@/routes';
import Card from '@/components/ui/Card.vue';

interface CharacterOption {
    value: string;
    label: string;
    description: string;
}

interface Stat {
    question: string;
    character_counts: Record<string, number>;
    response_counts: Record<number, number>;
    most_chosen_character: string;
    most_common_answer: number;
    average_answer: string;
    total_responses: number;
}

interface Props {
    statistics: Record<number, Stat>;
    characters: CharacterOption[];
}

const props = defineProps<Props>();

// Helper function to get character label from slug
const getCharacterLabel = (slug: string): string => {
    const character = props.characters.find(c => c.value === slug);
    return character ? character.label : 'Unknown Character';
};

const maxCount = (responses: Record<number, number>) => {
    const values = Object.values(responses);
    if (values.length === 0) {
        return 1;
    }
    return Math.max(...values);
};
</script>

<template>
    <Head title="Survey Statistics - Sandbox" />

    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <Card>
                <div class="text-center mb-8">
                    <h1 class="text-4xl font-bold text-white mb-4">
                        📊 Survey Statistics 📊
                    </h1>
                    <p class="text-purple-200 text-lg">
                        Insights from the Star Wars survey.
                    </p>
                    <div class="mt-4">
                        <Link
                            :href="character_statistics.url()"
                            class="text-purple-300 hover:text-purple-100 transition-colors duration-200"
                        >
                            View Character Statistics
                        </Link>
                    </div>
                </div>

                <div v-if="Object.keys(statistics).length === 0" class="text-center text-white">
                    <p>No survey data available yet. Be the first to submit a response!</p>
                </div>

                <div v-else class="space-y-8">
                    <div v-for="(stat, questionId) in statistics" :key="questionId" class="p-6 bg-white/5 rounded-lg border border-white/10">
                        <h2 class="text-xl font-semibold text-white mb-4">{{ stat.question }}</h2>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                            <div class="bg-white/10 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-purple-200 mb-2">Most Identified Character</h3>
                                <p class="text-2xl font-bold text-white">{{ getCharacterLabel(stat.most_chosen_character) }}</p>
                            </div>
                            <div class="bg-white/10 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-purple-200 mb-2">Most Common Answer</h3>
                                <p class="text-2xl font-bold text-white">{{ stat.most_common_answer }}</p>
                            </div>
                            <div class="bg-white/10 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-purple-200 mb-2">Average Answer</h3>
                                <p class="text-2xl font-bold text-white">{{ stat.average_answer }}</p>
                            </div>
                            <div class="bg-white/10 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-purple-200 mb-2">Total Responses</h3>
                                <p class="text-2xl font-bold text-white">{{ stat.total_responses }}</p>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-purple-200 mb-2">Responses Distribution (1-10)</h3>
                            <div v-if="Object.values(stat.response_counts).every(c => c === 0)">
                                <p class="text-purple-200 text-sm">No responses for this question yet.</p>
                            </div>
                            <div v-else class="flex items-end h-40 space-x-2">
                                <div v-for="rating in 10" :key="rating" class="flex-1 flex flex-col items-center">
                                    <div class="w-full h-full bg-white/10 rounded-t-md flex items-end">
                                        <div 
                                            class="w-full bg-gradient-to-t from-purple-500 to-pink-500 rounded-t-md"
                                            :style="{ height: (stat.response_counts[rating] / maxCount(stat.response_counts)) * 120 + 'px' }"
                                        ></div>
                                    </div>
                                    <span class="text-xs text-purple-300 mt-1">{{ rating }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </Card>
        </div>
    </div>
</template>
