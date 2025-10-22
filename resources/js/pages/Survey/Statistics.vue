<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { character_statistics } from '@/routes';
import Card from '@/components/ui/Card.vue';
import PageContainer from '@/components/ui/PageContainer.vue';
import PageHeader from '@/components/ui/PageHeader.vue';
import StatCard from '@/components/ui/StatCard.vue';
import Typography from '@/components/ui/Typography.vue';

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
    global_statistics: {
        total_responses: number;
        most_popular_character_overall: string;
    };
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

    <PageContainer>
        <div class="survey-content-wrapper">
            <Card>
                <PageHeader 
                    title="Survey Statistics" 
                    emoji="📊"
                    subtitle="Insights from the Star Wars survey."
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

                <div v-if="Object.keys(statistics).length === 0" class="text-center text-white">
                    <p>No survey data available yet. Be the first to submit a response!</p>
                </div>

                <div v-else class="space-y-8">

                    <Typography variant="h2">Global Statistics</Typography>
                    
                    <div class="survey-card-section">
                        <div class="stat-grid-2 mb-6">
                            <StatCard 
                                title="Total Responses" 
                                :value="global_statistics.total_responses"
                            />
                            <StatCard 
                                title="Most Popular Character Overall" 
                                :value="getCharacterLabel(global_statistics.most_popular_character_overall)"
                            />
                        </div>
                    </div>

                    <Typography variant="h2">Individual Question Statistics</Typography>


                    <div v-for="(stat, questionId) in statistics" :key="questionId" class="survey-card-section">
                        <Typography variant="h3" class="stat-title">{{ stat.question }}</Typography>

                        <div class="stat-grid-4 mb-6">
                            <StatCard 
                                title="Most Identified Character" 
                                :value="getCharacterLabel(stat.most_chosen_character)"
                            />
                            <StatCard 
                                title="Most Common Answer" 
                                :value="stat.most_common_answer"
                            />
                            <StatCard 
                                title="Average Answer" 
                                :value="stat.average_answer"
                            />
                            <StatCard 
                                title="Total Responses" 
                                :value="stat.total_responses"
                            />
                        </div>

                        <div>
                            <Typography variant="h4">Responses Distribution (1-10)</Typography>
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
    </PageContainer>
</template>
