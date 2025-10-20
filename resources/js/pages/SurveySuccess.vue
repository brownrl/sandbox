<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { survey, survey_statistics, character_statistics } from '@/routes';
import Card from '@/components/ui/Card.vue';
</script>

<template>
    <Head title="Survey Success - Sandbox" />
    
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto text-center">
            <Card>
                <!-- Success Icon -->
                <div class="mx-auto w-24 h-24 bg-green-500/20 rounded-full flex items-center justify-center mb-8">
                    <svg class="w-12 h-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                <!-- Success Message -->
                <h1 class="text-4xl font-bold text-white mb-4">
                    🎉 Thank You!
                </h1>
                
                <p class="text-xl text-purple-200 mb-6">
                    Your Star Wars survey has been submitted successfully!
                </p>
                
                <p class="text-purple-300 mb-8">
                    May the Force be with you, always. Your responses have been recorded in the galactic database.
                </p>

                <!-- Action Buttons -->
                <div class="space-y-4 sm:space-y-0 sm:space-x-4 sm:flex sm:justify-center">
                    <Link
                        :href="survey()"
                        class="inline-block px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-500 text-white font-semibold rounded-lg shadow-lg hover:from-purple-600 hover:to-pink-600 focus:outline-none focus:ring-4 focus:ring-purple-400/50 transition-all duration-200"
                    >
                        Take Survey Again
                    </Link>
                    
                    <Link
                        href="/"
                        class="inline-block px-6 py-3 bg-white/10 border border-white/20 text-white font-semibold rounded-lg hover:bg-white/20 focus:outline-none focus:ring-4 focus:ring-white/20 transition-all duration-200"
                    >
                        Return Home
                    </Link>

                    <Link
                        :href="survey_statistics.url()"
                        class="inline-block px-6 py-3 bg-white/10 border border-white/20 text-white font-semibold rounded-lg hover:bg-white/20 focus:outline-none focus:ring-4 focus:ring-white/20 transition-all duration-200"
                    >
                        View Statistics
                    </Link>

                    <Link
                        :href="character_statistics.url()"
                        class="inline-block px-6 py-3 bg-white/10 border border-white/20 text-white font-semibold rounded-lg hover:bg-white/20 focus:outline-none focus:ring-4 focus:ring-white/20 transition-all duration-200"
                    >
                        View Character Statistics
                    </Link>
                </div>

                <!-- Fun Quote -->
                <div class="mt-12 p-4 bg-white/5 rounded-lg border border-white/10">
                    <p class="text-purple-200 italic">
                        "Do or do not, there is no try." - Yoda
                    </p>
                    <p class="text-purple-300 text-sm mt-1">
                        And you definitely did! ✨
                    </p>
                </div>
            </Card>
        </div>
    </div>
</template>