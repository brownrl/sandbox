<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { survey, survey_statistics, character_statistics } from '@/routes';
import Card from '@/components/ui/Card.vue';
import PageContainer from '@/components/ui/PageContainer.vue';
import AppButton from '@/components/ui/AppButton.vue';
import Typography from '@/components/ui/Typography.vue';
</script>

<template>
    <Head title="Survey Success - Sandbox" />
    
    <PageContainer class="survey-page-container flex items-center justify-center">
        <div class="max-w-2xl mx-auto text-center">
            <Card class="survey-card-section">
                <!-- Success Icon -->
                <div class="success-icon bg-red-900/30 border-4 border-red-700">
                    <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                <!-- Success Message -->
                <Typography variant="display" class="text-red-600 mb-4 text-4xl font-bold">
                    ✨ Your Fate Is Sealed
                </Typography>
                
                <Typography variant="subtitle" class="text-gray-400 text-xl mb-6">
                    Your choices have been recorded in the dark archives...
                </Typography>
                
                <Typography variant="body" class="text-gray-500 mb-8 text-center italic">
                    The dark side clouds everything. Your responses have been absorbed into the void.
                </Typography>

                <!-- Action Buttons -->
                <div class="action-button-group space-y-4">
                    <Link :href="survey()">
                        <AppButton variant="primary" class="btn-primary">
                            Dare to Try Again
                        </AppButton>
                    </Link>
                    
                    <Link href="/">
                        <AppButton variant="secondary" class="bg-black/50 border-2 border-gray-700 text-gray-400 hover:bg-black hover:border-red-600 hover:text-red-500">
                            Escape to Safety
                        </AppButton>
                    </Link>

                    <Link :href="survey_statistics.url()">
                        <AppButton variant="secondary" class="bg-black/50 border-2 border-gray-700 text-gray-400 hover:bg-black hover:border-red-600 hover:text-red-500">
                            Witness the Statistics
                        </AppButton>
                    </Link>

                    <Link :href="character_statistics.url()">
                        <AppButton variant="secondary" class="bg-black/50 border-2 border-gray-700 text-gray-400 hover:bg-black hover:border-red-600 hover:text-red-500">
                            Character Archives
                        </AppButton>
                    </Link>
                </div>

                <!-- Dark Quote -->
                <div class="survey-card-section mt-12 bg-black/70 border-red-900">
                    <p class="text-red-500 italic text-lg">
                        "Fear is the path to the dark side... fear leads to anger... anger leads to hate... hate leads to suffering."
                    </p>
                    <p class="text-gray-500 text-sm mt-2">
                        - Yoda (and you have chosen your path) 🌑
                    </p>
                </div>
            </Card>
        </div>
    </PageContainer>
</template>