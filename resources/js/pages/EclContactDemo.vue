<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import EclLayout from '@/layouts/EclLayout.vue';
import { Link } from '@inertiajs/vue3';
import { eclDemo } from '@/routes';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

defineOptions({
    layout: EclLayout,
});

const page = usePage();

const props = defineProps<{
    title: string;
    errors?: Record<string, string>;
}>();

const form = ref({
    name: '',
    email: '',
    subject: '',
    message: '',
});

const processing = ref(false);

const submit = () => {
    processing.value = true;
    
    router.post('/ecl-contact-demo', form.value, {
        onSuccess: () => {
            // Form will be replaced with thank you message
        },
        onError: (errors) => {
            // Errors will be displayed automatically
            processing.value = false;
        },
        onFinish: () => {
            processing.value = false;
        },
    });
};
</script>

<template>
    <Head title="ECL Contact Form Demo - European Commission" />

    <!-- Page Header -->
    <div class="ecl-page-header">
        <div class="ecl-container">
            <nav class="ecl-page-header__breadcrumb ecl-breadcrumb" aria-label="You are here:" data-ecl-breadcrumb>
                <ol class="ecl-breadcrumb__container">
                    <li class="ecl-breadcrumb__segment" data-ecl-breadcrumb-item>
                        <Link :href="eclDemo()" class="ecl-link ecl-link--standalone ecl-link--no-visited ecl-breadcrumb__link">ECL Demo</Link>
                        <svg class="ecl-icon ecl-icon--xs ecl-icon--rotate-90 ecl-breadcrumb__icon"
                             focusable="false"
                             aria-hidden="true">
                            <use href="/ecl-assets/icons/icons.svg#corner-arrow"></use>
                        </svg>
                    </li>
                    <li class="ecl-breadcrumb__segment ecl-breadcrumb__current-page"
                        data-ecl-breadcrumb-item
                        aria-current="page">
                        Contact Form Demo
                    </li>
                </ol>
            </nav>
            <div class="ecl-page-header__body">
                <h1 class="ecl-page-header__title">Contact Form Demo</h1>
                <p class="ecl-page-header__description">
                    This page demonstrates ECL form components including text fields, text areas, and buttons in a contact form layout.
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="ecl-u-pv-xl">
        <div class="ecl-container">
            <!-- Thank You Message -->
            <div v-if="(page.props as any).flash?.form_submitted" class="ecl-u-bg-green-5 ecl-u-pa-m ecl-u-mb-xl" style="border-left: 4px solid #0d7f3f;">
                <h2 class="ecl-u-type-heading-2 ecl-u-mb-s">Thank You!</h2>
                <p class="ecl-u-type-paragraph ecl-u-mb-m">{{ (page.props as any).flash?.message }}</p>
                <Link :href="eclDemo()" class="ecl-link ecl-link--standalone">← Back to ECL Demo</Link>
            </div>

            <!-- Contact Form -->
            <template v-else>
                <h2 class="ecl-u-type-heading-2">Get in Touch</h2>
                <p class="ecl-u-type-paragraph-lead ecl-u-mb-l">
                    We'd love to hear from you. Send us a message and we'll respond as soon as possible.
                </p>

                <form class="ecl-form" @submit.prevent="submit" novalidate>
                    <!-- Name Field -->
                    <div class="ecl-form-group ecl-u-mb-m">
                        <label for="contact-name" id="contact-name-label" class="ecl-form-label">Full Name<span class="ecl-form-label__required" role="note" aria-label="required">*</span></label>
                        <div class="ecl-help-block" id="contact-name-helper">Please enter your full name as it appears on official documents.</div>
                        <input
                            id="contact-name"
                            class="ecl-text-input ecl-text-input--m"
                            type="text"
                            v-model="form.name"
                            placeholder="Enter your full name"
                            required
                            aria-describedby="contact-name-helper"
                            :class="{ 'ecl-text-input--invalid': errors?.name }"
                        />
                        <div v-if="errors?.name" class="ecl-feedback-message ecl-feedback-message--error ecl-u-mt-xs">
                            {{ errors.name }}
                        </div>
                    </div>

                    <!-- Email Field -->
                    <div class="ecl-form-group ecl-u-mb-m">
                        <label for="contact-email" id="contact-email-label" class="ecl-form-label">Email Address<span class="ecl-form-label__required" role="note" aria-label="required">*</span></label>
                        <div class="ecl-help-block" id="contact-email-helper">We'll use this email to respond to your message.</div>
                        <input
                            id="contact-email"
                            class="ecl-text-input ecl-text-input--m"
                            type="email"
                            v-model="form.email"
                            placeholder="your.email@example.com"
                            required
                            aria-describedby="contact-email-helper"
                            :class="{ 'ecl-text-input--invalid': errors?.email }"
                        />
                        <div v-if="errors?.email" class="ecl-feedback-message ecl-feedback-message--error ecl-u-mt-xs">
                            {{ errors.email }}
                        </div>
                    </div>

                    <!-- Subject Select -->
                    <div class="ecl-form-group ecl-u-mb-m">
                        <label for="contact-subject" id="contact-subject-label" class="ecl-form-label">Subject<span class="ecl-form-label__required" role="note" aria-label="required">*</span></label>
                        <div class="ecl-help-block" id="contact-subject-helper">Please select the most appropriate category for your inquiry.</div>
                        <div class="ecl-select__container ecl-select__container--m">
                            <select
                                class="ecl-select"
                                id="contact-subject"
                                v-model="form.subject"
                                name="subject"
                                required
                                aria-describedby="contact-subject-helper"
                                data-ecl-auto-init="Select"
                                :class="{ 'ecl-select--invalid': errors?.subject }"
                            >
                                <option value="">Select a subject</option>
                                <option value="general">General Inquiry</option>
                                <option value="support">Technical Support</option>
                                <option value="feedback">Feedback</option>
                                <option value="partnership">Partnership Opportunities</option>
                                <option value="other">Other</option>
                            </select>
                            <div class="ecl-select__icon">
                                <button class="ecl-button ecl-button--ghost ecl-button--icon-only" type="button" tabindex="-1">
                                    <span class="ecl-button__container">
                                        <span class="ecl-button__label" data-ecl-label="true">Toggle dropdown</span>
                                        <svg class="ecl-icon ecl-icon--xs ecl-icon--rotate-180 ecl-button__icon" focusable="false" aria-hidden="true" data-ecl-icon>
                                            <use xlink:href="/ecl-assets/icons/icons.svg#corner-arrow"></use>
                                        </svg>
                                    </span>
                                </button>
                            </div>
                        </div>
                        <div v-if="errors?.subject" class="ecl-feedback-message ecl-feedback-message--error ecl-u-mt-xs">
                            {{ errors.subject }}
                        </div>
                    </div>

                    <!-- Message Text Area -->
                    <div class="ecl-form-group ecl-u-mb-m">
                        <label for="contact-message" id="contact-message-label" class="ecl-form-label">Message<span class="ecl-form-label__required" role="note" aria-label="required">*</span></label>
                        <div class="ecl-help-block" id="contact-message-helper">Please provide details about your inquiry. Minimum 10 characters.</div>
                        <textarea
                            id="contact-message"
                            class="ecl-text-area ecl-text-area--m"
                            rows="6"
                            v-model="form.message"
                            placeholder="Enter your message here..."
                            required
                            minlength="10"
                            aria-describedby="contact-message-helper"
                            :class="{ 'ecl-text-area--invalid': errors?.message }"
                        ></textarea>
                        <div v-if="errors?.message" class="ecl-feedback-message ecl-feedback-message--error ecl-u-mt-xs">
                            {{ errors.message }}
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="ecl-form-group">
                        <button class="ecl-button ecl-button--primary" type="submit" :disabled="processing">
                            <span v-if="processing">Sending...</span>
                            <span v-else>Send Message</span>
                        </button>
                        <button class="ecl-button ecl-button--secondary ecl-u-ml-m" type="reset" @click="form = { name: '', email: '', subject: '', message: '' }">
                            Reset Form
                        </button>
                    </div>
                </form>
            </template>

            <div class="ecl-u-bg-blue-5 ecl-u-pa-m ecl-u-mt-xl" style="border-left: 4px solid #004494;">
                <h3 class="ecl-u-type-heading-4 ecl-u-mb-s">Form Features Demonstrated</h3>
                <ul class="ecl-unordered-list ecl-u-mb-none">
                    <li class="ecl-unordered-list__item">Required field validation with visual indicators</li>
                    <li class="ecl-unordered-list__item">Help text for user guidance</li>
                    <li class="ecl-unordered-list__item">Accessible form labels and ARIA attributes</li>
                    <li class="ecl-unordered-list__item">Primary and secondary button variants</li>
                    <li class="ecl-unordered-list__item">Responsive form layout</li>
                    <li class="ecl-unordered-list__item">Server-side validation with error display</li>
                    <li class="ecl-unordered-list__item">Success state with thank you message</li>
                </ul>
            </div>
        </div>
    </main>
</template>