<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import Card from '@/components/ui/Card.vue';

interface Question {
    id: number;
    question: string;
}

interface CharacterOption {
    value: string;
    label: string;
    description: string;
}

interface Props {
    questions: Question[];
    characters: CharacterOption[];
}

const props = defineProps<Props>();

const form = ref({
    first_name: '',
    character: '',
    question_responses: {} as Record<number, number>
});

const errors = ref({
    first_name: '',
    character: '',
    questions: ''
});

const processing = ref(false);

// Computed property to format data for submission
const formData = computed(() => ({
    first_name: form.value.first_name,
    character: form.value.character,
    questions: props.questions.map(q => q.id),
    responses: props.questions.map(q => form.value.question_responses[q.id] || 0)
}));

// Client-side validation
const validateForm = (): boolean => {
    // Clear previous errors
    errors.value = { first_name: '', character: '', questions: '' };
    let isValid = true;

    // Validate first name
    if (!form.value.first_name.trim()) {
        errors.value.first_name = 'Please enter your first name.';
        isValid = false;
    }

    // Validate character selection
    if (!form.value.character) {
        errors.value.character = 'Please select a Star Wars character.';
        isValid = false;
    }

    // Validate all questions are answered
    const unansweredCount = props.questions.filter(q => 
        !form.value.question_responses[q.id] || 
        form.value.question_responses[q.id] < 1 || 
        form.value.question_responses[q.id] > 10
    ).length;

    if (unansweredCount > 0) {
        errors.value.questions = `Please answer all ${props.questions.length} questions with a rating between 1 and 10.`;
        isValid = false;
    }

    return isValid;
};

const submitForm = () => {
    if (!validateForm()) {
        return;
    }

    processing.value = true;
    
    router.post('/survey', formData.value, {
        onSuccess: () => {
            // Success handled by redirect
        },
        onError: (serverErrors) => {
            // Handle server errors
            errors.value = { ...errors.value, ...serverErrors };
        },
        onFinish: () => {
            processing.value = false;
        }
    });
};
</script>

<template>
    <Head title="Star Wars Survey - Sandbox" />
    
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <Card>
                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 class="text-4xl font-bold text-white mb-4">
                        🌟 Star Wars Survey 🌟
                    </h1>
                    <p class="text-purple-200 text-lg">
                        Choose your Star Wars character and share your thoughts on some whimsical topics!
                    </p>
                    <p class="text-purple-300 text-sm mt-2">
                        Rate each statement from 1 (strongly disagree) to 10 (strongly agree)
                    </p>
                </div>

                <!-- Form -->
                <form @submit.prevent="submitForm">
                    <!-- Personal Information -->
                    <div class="mb-8 p-6 bg-white/5 rounded-lg border border-white/10">
                        <h2 class="text-2xl font-semibold text-white mb-4">About You</h2>
                        
                        <div class="space-y-6">
                            <!-- First Name -->
                            <div class="max-w-md">
                                <label for="first_name" class="block text-sm font-medium text-purple-200 mb-2">
                                    First Name *
                                </label>
                                <input
                                    id="first_name"
                                    type="text"
                                    v-model="form.first_name"
                                    name="first_name"
                                    class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-purple-300 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent"
                                    placeholder="Your first name"
                                    :class="{ 'border-red-400': errors.first_name }"
                                />
                                <div v-if="errors.first_name" class="mt-1 text-red-400 text-sm">
                                    {{ errors.first_name }}
                                </div>
                            </div>

                            <!-- Character Selection -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-purple-200 mb-4">
                                    Which Star Wars character do you most identify with? *
                                </label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                                    <label
                                        v-for="character in characters"
                                        :key="character.value"
                                        class="relative cursor-pointer flex"
                                    >
                                        <input
                                            type="radio"
                                            :value="character.value"
                                            v-model="form.character"
                                            class="sr-only"
                                        />
                                        <div
                                            class="p-4 bg-white/10 border-2 rounded-lg transition-all duration-200 hover:bg-white/20 w-full h-full"
                                            :class="[
                                                form.character === character.value
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
                                                <div class="font-medium text-white text-sm">{{ character.label }}</div>
                                                <div class="text-xs text-purple-300 mt-1">{{ character.description }}</div>
                                            </div>
                                            
                                            <!-- Selected Indicator -->
                                            <div
                                                v-if="form.character === character.value"
                                                class="absolute top-2 right-2 w-5 h-5 bg-purple-500 rounded-full flex items-center justify-center"
                                            >
                                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div v-if="errors.character" class="mt-2 text-red-400 text-sm">
                                    {{ errors.character }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Survey Questions -->
                    <div class="mb-8">
                        <h2 class="text-2xl font-semibold text-white mb-6">Survey Questions</h2>
                        
                        <div class="space-y-6">
                            <div
                                v-for="(question, index) in questions"
                                :key="question.id"
                                class="p-6 bg-white/5 rounded-lg border border-white/10"
                            >
                                <div class="mb-4">
                                    <h3 class="text-lg font-medium text-white mb-2">
                                        Question {{ index + 1 }}
                                    </h3>
                                    <p class="text-purple-200">
                                        {{ question.question }}
                                    </p>
                                </div>

                                <!-- Rating Scale -->
                                <div class="grid grid-cols-10 gap-2">
                                    <button
                                        v-for="rating in 10"
                                        :key="rating"
                                        type="button"
                                        @click="form.question_responses[question.id] = rating"
                                        class="aspect-square flex items-center justify-center rounded-lg border-2 transition-all duration-200 text-sm font-medium"
                                        :class="[
                                            form.question_responses[question.id] === rating
                                                ? 'bg-purple-500 border-purple-400 text-white shadow-lg'
                                                : 'bg-white/10 border-white/20 text-purple-200 hover:bg-white/20 hover:border-purple-400'
                                        ]"
                                    >
                                        {{ rating }}
                                    </button>
                                </div>

                                <div class="flex justify-between text-xs text-purple-300 mt-2">
                                    <span>Strongly Disagree</span>
                                    <span>Strongly Agree</span>
                                </div>

                                <div v-if="!form.question_responses[question.id] && errors.questions" class="mt-2 text-red-400 text-sm">
                                    Please rate this question
                                </div>
                            </div>
                        </div>

                        <div v-if="errors.questions" class="mt-4 text-red-400 text-sm text-center">
                            {{ errors.questions }}
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center">
                        <button
                            type="submit"
                            :disabled="processing"
                            class="px-8 py-4 bg-gradient-to-r from-purple-500 to-pink-500 text-white font-semibold rounded-lg shadow-lg hover:from-purple-600 hover:to-pink-600 focus:outline-none focus:ring-4 focus:ring-purple-400/50 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="processing">Submitting...</span>
                            <span v-else>Submit Survey</span>
                        </button>
                    </div>
                </form>
            </Card>
        </div>
    </div>
</template>