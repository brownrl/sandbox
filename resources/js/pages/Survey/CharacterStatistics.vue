<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { survey_statistics } from '@/routes';
import Card from '@/components/ui/Card.vue';
import PageContainer from '@/components/ui/PageContainer.vue';
import PageHeader from '@/components/ui/PageHeader.vue';
import CharacterSelector from '@/components/ui/CharacterSelector.vue';
import StatCard from '@/components/ui/StatCard.vue';
import Typography from '@/components/ui/Typography.vue';

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

    <PageContainer>
        <div class="survey-content-wrapper">
            <Card>
                <PageHeader 
                    title="Character Statistics" 
                    emoji="📊"
                    subtitle="Insights from the Star Wars survey for a specific character."
                >
                    <template #additional>
                        <div class="mt-4">
                            <Link
                                :href="survey_statistics.url()"
                                class="nav-link"
                            >
                                View General Statistics
                            </Link>
                        </div>
                    </template>
                </PageHeader>

                <!-- Character Selection -->
                <div class="mb-8 survey-card-section">
                    <CharacterSelector
                        v-model="selectedCharacter"
                        :characters="characters"
                        label="Select a character to see their survey statistics"
                    />
                </div>

                <div v-if="!selectedCharacter" class="text-center text-white">
                    <p>Please select a character to view their statistics.</p>
                </div>
                
                <div v-else-if="Object.keys(statistics).length === 0" class="text-center text-white">
                    <p>No survey data available for {{ getCharacterLabel(selectedCharacter) }}.</p>
                </div>

                <div v-else class="space-y-8">
                    <div v-for="(stat, questionId) in statistics" :key="questionId" class="survey-card-section">
                        <Typography variant="h3" class="stat-title">{{ stat.question }}</Typography>

                        <div class="stat-grid-3 mb-6">
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
