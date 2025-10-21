<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { survey_statistics } from '@/routes';
import Card from '@/components/ui/Card.vue';

interface CharacterOption {
    value: string;
    label: string;
    description: string;
}

interface Stat {
    question: string;
    response_counts: Record<number, number>;
    most_common_answer: number;
    average_answer: string;
    total_responses: number;
}

interface Props {
    statistics: Record<number, Stat>;
    character: string | null;
    characters: CharacterOption[];
}

const props = defineProps<Props>();

const selectedCharacter = ref(props.character);

// Helper function to get character label from slug
const getCharacterLabel = (slug: string): string => {
    const character = props.characters.find(c => c.value === slug);
    return character ? character.label : 'Unknown Character';
};

watch(selectedCharacter, (newCharacter) => {
    router.visit('/character-statistics', {
        data: { character: newCharacter },
        preserveState: true,
        preserveScroll: true,
    });
});

const maxCount = (responses: Record<number, number>) => {
    const values = Object.values(responses);
    if (values.length === 0) {
        return 1;
    }
    return Math.max(...values);
};
</script>

<template>
    <Head title="Character Statistics - Sandbox" />

    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <Card>
                <div class="text-center mb-8">
                    <h1 class="text-4xl font-bold text-white mb-4">
                        📊 Character Statistics 📊
                    </h1>
                    <p class="text-purple-200 text-lg">
                        Insights from the Star Wars survey for a specific character.
                    </p>
                    <div class="mt-4">
                        <Link
                            :href="survey_statistics.url()"
                            class="text-purple-300 hover:text-purple-100 transition-colors duration-200"
                        >
                            View General Statistics
                        </Link>
                    </div>
                </div>

                <!-- Character Selection -->
                <div class="mb-8 p-6 bg-white/5 rounded-lg border border-white/10">
                    <label class="block text-sm font-medium text-purple-200 mb-4">
                        Select a character to see their survey statistics
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                        <label
                            v-for="characterOption in characters"
                            :key="characterOption.value"
                            class="relative cursor-pointer flex"
                        >
                            <input
                                type="radio"
                                :value="characterOption.value"
                                v-model="selectedCharacter"
                                class="sr-only"
                            />
                            <div
                                class="p-4 bg-white/10 border-2 rounded-lg transition-all duration-200 hover:bg-white/20 w-full h-full"
                                :class="[
                                    selectedCharacter === characterOption.value
                                        ? 'border-purple-400 bg-purple-500/20'
                                        : 'border-white/20'
                                ]"
                            >
                                <!-- Image Placeholder -->
                                <div class="w-16 h-16 mx-auto mb-3 bg-white/20 rounded-full flex items-center justify-center border border-white/30">
                                    <svg class="w-8 h-8 text-purple-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                
                                <!-- Character Name -->
                                <div class="text-center">
                                    <div class="font-medium text-white text-sm">{{ characterOption.label }}</div>
                                    <div class="text-xs text-purple-300 mt-1">{{ characterOption.description }}</div>
                                </div>
                                
                                <!-- Selected Indicator -->
                                <div
                                    v-if="selectedCharacter === characterOption.value"
                                    class="absolute top-2 right-2 w-5 h-5 bg-purple-500 rounded-full flex items-center justify-center"
                                >
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <div v-if="!selectedCharacter" class="text-center text-white">
                    <p>Please select a character to view their statistics.</p>
                </div>
                
                <div v-else-if="Object.keys(statistics).length === 0" class="text-center text-white">
                    <p>No survey data available for {{ getCharacterLabel(selectedCharacter) }}.</p>
                </div>

                <div v-else class="space-y-8">
                    <div v-for="(stat, questionId) in statistics" :key="questionId" class="p-6 bg-white/5 rounded-lg border border-white/10">
                        <h2 class="text-xl font-semibold text-white mb-4">{{ stat.question }}</h2>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
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
