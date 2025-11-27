<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import EclLayout from '@/layouts/EclLayout.vue';
import { Link } from '@inertiajs/vue3';
import { home, eclTableDemo } from '@/routes';

defineOptions({
    layout: EclLayout,
});
</script>

<template>
    <Head title="ECL Demo - European Commission" />

    <!-- Page Header -->
    <div class="ecl-page-header">
        <div class="ecl-container">
            <nav class="ecl-page-header__breadcrumb ecl-breadcrumb" aria-label="You are here:" data-ecl-breadcrumb>
                <ol class="ecl-breadcrumb__container">
                    <li class="ecl-breadcrumb__segment" data-ecl-breadcrumb-item>
                        <Link :href="home()" class="ecl-link ecl-link--standalone ecl-link--no-visited ecl-breadcrumb__link">Home</Link>
                        <svg class="ecl-icon ecl-icon--xs ecl-icon--rotate-90 ecl-breadcrumb__icon" 
                             focusable="false" 
                             aria-hidden="true">
                            <use href="/ecl-assets/icons/icons.svg#corner-arrow"></use>
                        </svg>
                    </li>
                    <li class="ecl-breadcrumb__segment ecl-breadcrumb__current-page" 
                        data-ecl-breadcrumb-item 
                        aria-current="page">
                        ECL Demo
                    </li>
                </ol>
            </nav>
            <div class="ecl-page-header__body">
                <h1 class="ecl-page-header__title">EC Europa Component Library Demo</h1>
                <p class="ecl-page-header__description">
                    This page demonstrates the European Commission's design system with all ECL components correctly loaded and initialized.
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="ecl-u-pv-xl">
        <div class="ecl-container">
            <h2 class="ecl-u-type-heading-2">Welcome to ECL</h2>
            <p class="ecl-u-type-paragraph-lead">
                The EC Europa Component Library provides all the design elements needed to build consistent, accessible, 
                and user-friendly European Commission websites.
            </p>

            <h3 class="ecl-u-type-heading-3 ecl-u-mt-l">Typography Examples</h3>
            <p class="ecl-u-type-paragraph">
                This is a standard paragraph using ECL typography utilities. Lorem ipsum dolor sit amet, consectetur 
                adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
            </p>
            <p class="ecl-u-type-paragraph">
                Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. 
                Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
            </p>
            <p class="ecl-u-type-paragraph-m">
                This paragraph uses the medium size variant for emphasis. All typography follows European accessibility 
                standards and responsive design principles.
            </p>

            <h3 class="ecl-u-type-heading-3 ecl-u-mt-l">Features</h3>
            <ul class="ecl-unordered-list">
                <li class="ecl-unordered-list__item">Consistent design across all EC websites</li>
                <li class="ecl-unordered-list__item">WCAG 2.1 AA accessibility compliant</li>
                <li class="ecl-unordered-list__item">Mobile-first responsive components</li>
                <li class="ecl-unordered-list__item">Comprehensive component library</li>
                <li class="ecl-unordered-list__item">Multi-language support</li>
            </ul>

            <div class="ecl-u-bg-blue-5 ecl-u-pa-m ecl-u-mt-l" style="border-left: 4px solid #004494;">
                <p class="ecl-u-type-paragraph ecl-u-mb-s">
                    <strong>Explore More Components:</strong>
                </p>
                <p class="ecl-u-type-paragraph">
                    See comprehensive table examples including default, zebra-striped, multi-header, and sortable tables in the 
                    <Link :href="eclTableDemo()" class="ecl-link ecl-link--standalone">ECL Table Demo</Link>.
                </p>
            </div>

            <h3 class="ecl-u-type-heading-3 ecl-u-mt-l">About Lorem Ipsum</h3>
            <p class="ecl-u-type-paragraph">
                Lorem Ipsum is simply dummy text of the printing and typesetting industry. It has been the industry's 
                standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled 
                it to make a type specimen book.
            </p>
            <p class="ecl-u-type-paragraph">
                Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical 
                Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at 
                Hampden-Sydney College in Virginia, discovered the undoubtable source.
            </p>

            <h4 class="ecl-u-type-heading-4 ecl-u-mt-m">Standard Passage</h4>
            <p class="ecl-u-type-paragraph">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et 
                dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip 
                ex ea commodo consequat.
            </p>
            <p class="ecl-u-type-paragraph">
                Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. 
                Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
            </p>

            <h3 class="ecl-u-type-heading-3 ecl-u-mt-xl">Interactive Accordion Test</h3>
            <p class="ecl-u-type-paragraph ecl-u-mb-m">
                This accordion component tests ECL JavaScript initialization. Click the items below to expand/collapse them.
            </p>

            <div class="ecl-accordion" data-ecl-auto-init="Accordion" data-ecl-accordion>
                <div class="ecl-accordion__item">
                    <h3 class="ecl-accordion__title" id="accordion-example-title1">
                        <button type="button" 
                                class="ecl-accordion__toggle" 
                                data-ecl-accordion-toggle 
                                aria-controls="accordion-example-content1">
                            <span class="ecl-accordion__toggle-flex">
                                <span class="ecl-accordion__toggle-title">
                                    First Accordion Item
                                </span>
                                <span class="ecl-accordion__toggle-indicator">
                                    <svg class="ecl-icon ecl-icon--s ecl-accordion__toggle-icon" 
                                         focusable="false" 
                                         aria-hidden="true" 
                                         data-ecl-accordion-icon>
                                        <use href="/ecl-assets/icons/icons.svg#plus"></use>
                                    </svg>
                                    <svg class="ecl-icon ecl-icon--s ecl-accordion__toggle-icon" 
                                         focusable="false" 
                                         aria-hidden="true" 
                                         data-ecl-accordion-icon>
                                        <use href="/ecl-assets/icons/icons.svg#minus"></use>
                                    </svg>
                                </span>
                            </span>
                        </button>
                    </h3>
                    <div class="ecl-accordion__content" 
                         hidden 
                         id="accordion-example-content1" 
                         role="region" 
                         aria-labelledby="accordion-example-title1">
                        <div class="ecl-accordion__body">
                            <p class="ecl-u-type-paragraph">
                                This is the content of the first accordion item. If you can see this text after clicking 
                                the button above, the ECL JavaScript is working correctly.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="ecl-accordion__item">
                    <h3 class="ecl-accordion__title" id="accordion-example-title2">
                        <button type="button" 
                                class="ecl-accordion__toggle" 
                                data-ecl-accordion-toggle 
                                aria-controls="accordion-example-content2">
                            <span class="ecl-accordion__toggle-flex">
                                <span class="ecl-accordion__toggle-title">
                                    Second Accordion Item
                                </span>
                                <span class="ecl-accordion__toggle-indicator">
                                    <svg class="ecl-icon ecl-icon--s ecl-accordion__toggle-icon" 
                                         focusable="false" 
                                         aria-hidden="true" 
                                         data-ecl-accordion-icon>
                                        <use href="/ecl-assets/icons/icons.svg#plus"></use>
                                    </svg>
                                    <svg class="ecl-icon ecl-icon--s ecl-accordion__toggle-icon" 
                                         focusable="false" 
                                         aria-hidden="true" 
                                         data-ecl-accordion-icon>
                                        <use href="/ecl-assets/icons/icons.svg#minus"></use>
                                    </svg>
                                </span>
                            </span>
                        </button>
                    </h3>
                    <div class="ecl-accordion__content" 
                         hidden 
                         id="accordion-example-content2" 
                         role="region" 
                         aria-labelledby="accordion-example-title2">
                        <div class="ecl-accordion__body">
                            <p class="ecl-u-type-paragraph">
                                This is the content of the second accordion item. The accordion component uses the same 
                                ECL.autoInit() mechanism as the header components.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="ecl-accordion__item">
                    <h3 class="ecl-accordion__title" id="accordion-example-title3">
                        <button type="button" 
                                class="ecl-accordion__toggle" 
                                data-ecl-accordion-toggle 
                                aria-controls="accordion-example-content3">
                            <span class="ecl-accordion__toggle-flex">
                                <span class="ecl-accordion__toggle-title">
                                    Third Accordion Item
                                </span>
                                <span class="ecl-accordion__toggle-indicator">
                                    <svg class="ecl-icon ecl-icon--s ecl-accordion__toggle-icon" 
                                         focusable="false" 
                                         aria-hidden="true" 
                                         data-ecl-accordion-icon>
                                        <use href="/ecl-assets/icons/icons.svg#plus"></use>
                                    </svg>
                                    <svg class="ecl-icon ecl-icon--s ecl-accordion__toggle-icon" 
                                         focusable="false" 
                                         aria-hidden="true" 
                                         data-ecl-accordion-icon>
                                        <use href="/ecl-assets/icons/icons.svg#minus"></use>
                                    </svg>
                                </span>
                            </span>
                        </button>
                    </h3>
                    <div class="ecl-accordion__content" 
                         hidden 
                         id="accordion-example-content3" 
                         role="region" 
                         aria-labelledby="accordion-example-title3">
                        <div class="ecl-accordion__body">
                            <p class="ecl-u-type-paragraph">
                                This is the content of the third accordion item. If this accordion works but the header 
                                components don't, it suggests the issue is specific to layout vs. page component initialization.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>
