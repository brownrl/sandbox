<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted, nextTick } from 'vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { survey_statistics } from '@/routes';
import Card from '@/components/ui/Card.vue';
import PageContainer from '@/components/ui/PageContainer.vue';
import PageHeader from '@/components/ui/PageHeader.vue';
import CharacterSelector from '@/components/ui/CharacterSelector.vue';
import StatCard from '@/components/ui/StatCard.vue';
import Typography from '@/components/ui/Typography.vue';
import StarWarsThemeSwitcher from '@/components/ui/StarWarsThemeSwitcher.vue';
import { useStarWarsTheme } from '@/composables/useStarWarsTheme';

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
    totalSurveys: number;
}

const props = defineProps<Props>();

const { theme, isLightSide } = useStarWarsTheme();

const selectedCharacter = ref(props.character);
const characterInfoRef = ref<HTMLElement | null>(null);
const showScrollToTop = ref(false);

// Helper function to get character label from slug
const getCharacterLabel = (slug: string): string => {
    const character = props.characters.find(c => c.value === slug);
    return character ? character.label : 'Unknown Character';
};

// Helper function to get character description from slug
const getCharacterDescription = (slug: string): string => {
    const character = props.characters.find(c => c.value === slug);
    return character ? character.description : '';
};

watch(selectedCharacter, (newCharacter) => {
    router.visit('/character-statistics', {
        data: { character: newCharacter },
        preserveState: true,
        preserveScroll: false,
        onSuccess: () => {
            nextTick(() => {
                if (characterInfoRef.value) {
                    characterInfoRef.value.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        }
    });
});

const handleScroll = () => {
    showScrollToTop.value = window.scrollY > 300;
};

const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    handleScroll();
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
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

    <div :class="{ 'light-side': isLightSide() }">
        <PageContainer class="survey-page-container">
            <!-- Theme Switcher -->
            <div class="fixed top-4 right-4 z-50">
                <StarWarsThemeSwitcher />
            </div>

            <div class="survey-content-wrapper">
            <Card class="survey-card-section">
                <PageHeader 
                    title="Character Statistics" 
                    emoji="📊"
                    subtitle="Insights from the Star Wars survey for a specific character."
                    class="survey-title"
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

                <div v-if="!selectedCharacter" class="text-center text-red-500">
                    <p>Please select a character to view their statistics.</p>
                </div>
                
                <div v-else-if="Object.keys(statistics).length === 0" class="text-center text-red-500">
                    <p>No survey data available for {{ getCharacterLabel(selectedCharacter) }}.</p>
                </div>

                <div v-else class="space-y-8">
                    <!-- Character Info Card -->
                    <div 
                        ref="characterInfoRef" 
                        class="survey-card-section rounded-lg p-6"
                    >
                        <div class="flex items-center gap-6">
                            <div 
                                class="w-24 h-24 rounded-full overflow-hidden border-4 shrink-0"
                                :class="isLightSide() ? 'border-blue-500' : 'border-red-700'"
                            >
                                <img
                                    :src="`/storage/sw/${selectedCharacter}-opt.jpg`"
                                    :alt="getCharacterLabel(selectedCharacter)"
                                    class="w-full h-full object-cover"
                                    @error="(e) => {
                                        const target = e.target as HTMLImageElement;
                                        target.style.display = 'none';
                                        (target.nextElementSibling as HTMLElement).style.display = 'flex';
                                    }"
                                />
                                <div class="w-full h-full bg-black/20 items-center justify-center" style="display: none;">
                                    <svg 
                                        class="w-12 h-12"
                                        :class="isLightSide() ? 'text-blue-500' : 'text-red-500'"
                                        fill="currentColor" 
                                        viewBox="0 0 20 20"
                                    >
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <Typography 
                                    variant="h2" 
                                    class="mb-2"
                                    :class="isLightSide() ? 'text-blue-600' : 'text-red-500'"
                                >
                                    {{ getCharacterLabel(selectedCharacter) }}
                                </Typography>
                                <p 
                                    class="text-sm mb-3"
                                    :class="isLightSide() ? 'text-gray-700' : 'text-gray-400'"
                                >
                                    {{ getCharacterDescription(selectedCharacter) }}
                                </p>
                                <div 
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full"
                                    :class="isLightSide() ? 'bg-blue-100' : 'bg-red-700/30'"
                                >
                                    <svg 
                                        class="w-5 h-5"
                                        :class="isLightSide() ? 'text-blue-600' : 'text-red-500'"
                                        fill="currentColor" 
                                        viewBox="0 0 20 20"
                                    >
                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                                    </svg>
                                    <span 
                                        class="font-semibold"
                                        :class="isLightSide() ? 'text-blue-900' : 'text-white'"
                                    >{{ totalSurveys }}</span>
                                    <span 
                                        :class="isLightSide() ? 'text-gray-600' : 'text-gray-400'"
                                    >{{ totalSurveys === 1 ? 'Survey' : 'Surveys' }} Taken</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-for="(stat, questionId) in statistics" :key="questionId" class="survey-card-section">
                        <Typography variant="h3" class="stat-title">{{ stat.question }}</Typography>

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

        <!-- Floating Scroll to Top Button -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 scale-90"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-90"
        >
            <button
                v-if="showScrollToTop"
                @click="scrollToTop"
                class="fixed bottom-8 right-8 z-50 px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white font-semibold rounded-full shadow-lg hover:shadow-xl transition-all duration-200 flex items-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                </svg>
                Choose New Character
            </button>
        </Transition>
    </PageContainer>
    </div>
</template>
