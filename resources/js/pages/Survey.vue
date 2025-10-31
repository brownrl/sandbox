<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import Card from '@/components/ui/Card.vue';
import PageContainer from '@/components/ui/PageContainer.vue';
import PageHeader from '@/components/ui/PageHeader.vue';
import CharacterSelector from '@/components/ui/CharacterSelector.vue';
import RatingScale from '@/components/ui/RatingScale.vue';
import AppButton from '@/components/ui/AppButton.vue';
import Typography from '@/components/ui/Typography.vue';
import StarWarsThemeSwitcher from '@/components/ui/StarWarsThemeSwitcher.vue';
import { useStarWarsTheme } from '@/composables/useStarWarsTheme';

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

const { theme, isLightSide } = useStarWarsTheme();

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
    
    <div :class="{ 'light-side': isLightSide() }">
        <PageContainer class="survey-page-container">
            <!-- Theme Switcher -->
            <div class="fixed top-4 right-4 z-50">
                <StarWarsThemeSwitcher />
            </div>

            <div class="max-w-4xl mx-auto">
                <Card class="survey-card-section">
                <!-- Header -->
                <PageHeader 
                    title="Star Wars Survey" 
                    emoji="✨"
                    subtitle="Choose your Star Wars character and share your thoughts on some whimsical topics!"
                    class="survey-title"
                >
                    <template #additional>
                        <p class="survey-subtitle">
                            Rate each statement from 1 (strongly disagree) to 10 (strongly agree)
                        </p>
                    </template>
                </PageHeader>

                <!-- Form -->
                <form @submit.prevent="submitForm">
                    <!-- Personal Information -->
                    <div class="mb-8 survey-card-section">
                        <Typography variant="h2" class="section-title">About You</Typography>
                        
                        <div class="space-y-6">
                            <!-- First Name -->
                            <div class="max-w-md">
                                <label for="first_name" class="form-label">
                                    First Name *
                                </label>
                                <input
                                    id="first_name"
                                    type="text"
                                    v-model="form.first_name"
                                    name="first_name"
                                    class="form-input"
                                    placeholder="Your first name"
                                    :class="{ 'border-red-400': errors.first_name }"
                                />
                                <div v-if="errors.first_name" class="form-error">
                                    {{ errors.first_name }}
                                </div>
                            </div>

                            <!-- Character Selection -->
                            <div class="md:col-span-2">
                                <CharacterSelector
                                    v-model="form.character"
                                    :characters="characters"
                                    label="Which Star Wars character do you most identify with? *"
                                />
                                <div v-if="errors.character" class="form-error">
                                    {{ errors.character }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Survey Questions -->
                    <div class="mb-8">
                        <Typography variant="h2" class="section-title">Survey Questions</Typography>
                        
                        <div class="space-y-6">
                            <RatingScale
                                v-for="(question, index) in questions"
                                :key="question.id"
                                v-model="form.question_responses[question.id]"
                                :question-text="question.question"
                                :question-number="index + 1"
                                :error="!form.question_responses[question.id] && errors.questions ? 'Please rate this question' : ''"
                            />
                        </div>

                        <div v-if="errors.questions" class="mt-4 text-red-400 text-sm text-center">
                            {{ errors.questions }}
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center">
                        <AppButton
                            type="submit"
                            :disabled="processing"
                            :loading="processing"
                            class="btn-primary"
                        >
                            <span v-if="processing">Submitting...</span>
                            <span v-else>Submit Survey</span>
                        </AppButton>
                    </div>
                </form>
            </Card>
        </div>
    </PageContainer>
    </div>
</template>