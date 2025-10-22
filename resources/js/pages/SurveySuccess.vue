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
    
    <PageContainer class="flex items-center justify-center">
        <div class="max-w-2xl mx-auto text-center">
            <Card>
                <!-- Success Icon -->
                <div class="success-icon">
                    <svg class="w-12 h-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                <!-- Success Message -->
                <Typography variant="display" class="success-message">
                    🎉 Thank You!
                </Typography>
                
                <Typography variant="subtitle" class="success-description">
                    Your Star Wars survey has been submitted successfully!
                </Typography>
                
                <Typography variant="body" class="success-note text-center">
                    May the Force be with you, always. Your responses have been recorded in the galactic database.
                </Typography>

                <!-- Action Buttons -->
                <div class="action-button-group">
                    <Link :href="survey()">
                        <AppButton variant="primary">
                            Take Survey Again
                        </AppButton>
                    </Link>
                    
                    <Link href="/">
                        <AppButton variant="secondary">
                            Return Home
                        </AppButton>
                    </Link>

                    <Link :href="survey_statistics.url()">
                        <AppButton variant="secondary">
                            View Statistics
                        </AppButton>
                    </Link>

                    <Link :href="character_statistics.url()">
                        <AppButton variant="secondary">
                            View Character Statistics
                        </AppButton>
                    </Link>
                </div>

                <!-- Fun Quote -->
                <div class="info-box mt-12">
                    <p class="quote-text">
                        "Do or do not, there is no try." - Yoda
                    </p>
                    <p class="quote-author">
                        And you definitely did! ✨
                    </p>
                </div>
            </Card>
        </div>
    </PageContainer>
</template>