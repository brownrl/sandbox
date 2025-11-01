<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { character_statistics } from '@/routes';
import Card from '@/components/ui/Card.vue';
import PageContainer from '@/components/ui/PageContainer.vue';
import PageHeader from '@/components/ui/PageHeader.vue';
import StatCard from '@/components/ui/StatCard.vue';
import Typography from '@/components/ui/Typography.vue';
import StarWarsThemeSwitcher from '@/components/ui/StarWarsThemeSwitcher.vue';
import CharacterDisplay from '@/components/ui/CharacterDisplay.vue';
import { useStarWarsTheme } from '@/composables/useStarWarsTheme';

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
    most_chosen_character_description: string;
    most_common_answer: number;
    average_answer: string;
    total_responses: number;
}

interface Props {
    statistics: Record<number, Stat>;
    characters: CharacterOption[];
    global_statistics: {
        total_surveys: number;
        total_question_responses: number;
        most_popular_character_overall: string;
        most_popular_character_count: number;
        least_popular_character_overall: string;
        least_popular_character_count: number;
    };
}

const props = defineProps<Props>();

const { theme, isLightSide } = useStarWarsTheme();

// Helper function to get character label from slug

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

    <div :class="{ 'light-side': isLightSide() }">
        <PageContainer class="survey-page-container">
            <!-- Theme Switcher -->
            <div class="fixed top-4 right-4 z-50">
                <StarWarsThemeSwitcher />
            </div>

            <div class="survey-content-wrapper">
            <Card class="survey-card-section">
                <PageHeader 
                    title="Survey Statistics" 
                    emoji="📊"
                    subtitle="Insights from the Star Wars survey."
                    class="survey-title"
                >
                    <template #additional>
                        <div class="mt-4">
                            <Link
                                :href="character_statistics.url()"
                                class="nav-link"
                            >
                                View Character Statistics
                            </Link>
                        </div>
                    </template>
                </PageHeader>

                <div v-if="Object.keys(statistics).length === 0" class="text-center text-red-500">
                    <p>No survey data available yet. Be the first to submit a response!</p>
                </div>

                <div v-else class="space-y-8">

                    <Typography variant="h2" class="section-title">Global Statistics</Typography>
                    
                    <div class="survey-card-section">
                        <div class="stat-grid-2 mb-6">
                            <StatCard 
                                title="Total Surveys Taken" 
                                :value="global_statistics.total_surveys"
                                class="stat-value"
                            />
                            <StatCard 
                                title="Total Question Responses" 
                                :value="global_statistics.total_question_responses"
                                class="stat-value"
                            />
                        </div>
                        <div class="stat-grid-2 mb-6">
                            <div>
                                <Typography variant="h3" class="text-sm font-medium text-gray-400 mb-2">Most Popular Character</Typography>
                                <CharacterDisplay 
                                    :character-slug="global_statistics.most_popular_character_overall"
                                    :character-label="getCharacterLabel(global_statistics.most_popular_character_overall)"
                                    :count="global_statistics.most_popular_character_count"
                                />
                            </div>
                            <div>
                                <Typography variant="h3" class="text-sm font-medium text-gray-400 mb-2">Least Popular Character</Typography>
                                <CharacterDisplay 
                                    :character-slug="global_statistics.least_popular_character_overall"
                                    :character-label="getCharacterLabel(global_statistics.least_popular_character_overall)"
                                    :count="global_statistics.least_popular_character_count"
                                />
                            </div>
                        </div>
                    </div>

                    <Typography variant="h2" class="section-title">Individual Question Statistics</Typography>


                    <div v-for="(stat, questionId) in statistics" :key="questionId" class="survey-card-section">
                        <Typography variant="h3" class="stat-title">{{ stat.question }}</Typography>

                        <div class="mb-6">
                            <Typography variant="h4" class="text-sm font-medium text-gray-400 mb-2">Most Identified Character</Typography>
                            <CharacterDisplay 
                                :character-slug="stat.most_chosen_character"
                                :character-label="getCharacterLabel(stat.most_chosen_character)"
                                :subtitle="stat.most_chosen_character_description"
                                class="mb-6"
                            />
                        </div>

                        <div class="stat-grid-3 mb-6">
                            <StatCard 
                                title="Most Common Answer" 
                                :value="stat.most_common_answer"
                                class="stat-value"
                            />
                            <StatCard 
                                title="Average Answer" 
                                :value="stat.average_answer"
                                class="stat-value"
                            />
                            <StatCard 
                                title="Total Responses" 
                                :value="stat.total_responses"
                                class="stat-value"
                            />
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <Typography variant="h4">Responses Distribution (1-10)</Typography>
                                <div class="flex items-center gap-2 text-sm">
                                    <span class="text-gray-400">Average:</span>
                                    <span class="text-purple-400 font-semibold">{{ stat.average_answer }}</span>
                                    <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                                </div>
                            </div>
                            <div v-if="Object.values(stat.response_counts).every(c => c === 0)">
                                <p class="text-red-500 text-sm">No responses for this question yet.</p>
                            </div>
                            <div v-else class="chart-container relative">
                                <!-- Average line indicator -->
                                <div 
                                    class="absolute top-0 bottom-8 w-0.5 bg-purple-500 z-10"
                                    :style="{ left: `calc(${((stat.average_answer - 0.5) / 10) * 100}% - 1px)` }"
                                >
                                    <div class="absolute -top-6 left-1/2 -translate-x-1/2 whitespace-nowrap">
                                        <div class="bg-purple-500 text-white text-xs px-2 py-1 rounded">
                                            Avg: {{ stat.average_answer }}
                                        </div>
                                    </div>
                                </div>
                                
                                <div v-for="rating in 10" :key="rating" class="chart-bar-container">
                                    <div class="chart-bar-background">
                                        <div 
                                            class="chart-bar"
                                            :class="{ 'bg-purple-600': Math.abs(rating - stat.average_answer) < 0.5 }"
                                            :style="{ height: (stat.response_counts[rating] / maxCount(stat.response_counts)) * 120 + 'px' }"
                                        ></div>
                                    </div>
                                    <span class="chart-label">{{ rating }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </Card>
        </div>
    </PageContainer>
    </div>
</template>
