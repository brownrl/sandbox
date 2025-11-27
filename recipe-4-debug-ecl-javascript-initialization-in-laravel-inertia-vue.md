# Recipe 4: Debug ECL JavaScript Initialization in Laravel Inertia Vue

## Description
Complete guide to debugging and fixing ECL (EC Europa Component Library) JavaScript initialization issues in a Laravel + Inertia.js + Vue environment. Covers the discovery that ECL components in layout components don't initialize, while page-level components work correctly, plus all structural fixes needed for proper ECL component rendering.

## Content

## Problem Overview

When integrating ECL (EC Europa Component Library) v4.11.1 into a Laravel + Inertia.js + Vue 3 application with an isolated ecosystem, ECL JavaScript components were not initializing properly. The language selector dropdown, search toggle, and navigation menu were non-functional despite correct setup.

## Critical Discovery

**ECL components in Vue layout components (EclLayout.vue → EclHeader.vue) DO NOT initialize, but ECL components directly in page components (EclDemo.vue) DO initialize correctly.**

This suggests that:
1. ECL.autoInit() runs before layout components are fully mounted in the DOM
2. Vue's virtual DOM rendering sequence affects ECL's ability to find and bind components
3. Layout components may need manual initialization via Vue lifecycle hooks

## Testing Approach

To diagnose the issue:
1. Added an accordion component directly to the page content (EclDemo.vue)
2. The accordion worked perfectly - expanding/collapsing on click
3. Confirmed that ECL JavaScript was loaded and functional
4. Isolated the issue to layout-level components vs. page-level components

## ECL.autoInit() Setup (What We Tried)

**In resources/views/ecl.blade.php:**

```html
<script src="/ecl-assets/js/ecl-ec.js"></script>
<script>
    // Initial page load
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof ECL !== 'undefined' && typeof ECL.autoInit === 'function') {
            ECL.autoInit();
        }
    });

    // Inertia client-side navigation
    document.addEventListener('inertia:navigate', function () {
        setTimeout(function() {
            if (typeof ECL !== 'undefined' && typeof ECL.autoInit === 'function') {
                ECL.autoInit();
            }
        }, 50);
    });
</script>
```

**Result:** This setup works for page-level components but NOT for layout-level components.

## Component Structure Fixes

### 1. Language Selector - Complete Structure Required

**WRONG** - Missing language list overlay:
```html
<div class="ecl-site-header__language">
    <a class="ecl-button ecl-button--tertiary ecl-site-header__language-selector" 
       href="#" 
       data-ecl-language-selector>
        EN
    </a>
    <!-- MISSING: Language list overlay! -->
</div>
```

**CORRECT** - Complete with overlay and language options:
```html
<div class="ecl-site-header__language">
    <a class="ecl-button ecl-button--tertiary ecl-site-header__language-selector" 
       href="#" 
       data-ecl-language-selector 
       role="button" 
       aria-controls="language-list-overlay">
        <span class="ecl-site-header__language-icon">
            <svg class="ecl-icon ecl-icon--s ecl-site-header__icon">
                <use href="/ecl-assets/icons/icons.svg#global"></use>
            </svg>
        </span>
        EN
    </a>
    
    <!-- Language list overlay (hidden by default) -->
    <div class="ecl-site-header__language-container" 
         id="language-list-overlay" 
         hidden 
         data-ecl-language-list-overlay 
         role="dialog">
        <div class="ecl-site-header__language-header">
            <div class="ecl-site-header__language-title">
                Select your language
            </div>
            <button class="ecl-button ecl-button--tertiary ecl-site-header__language-close" 
                    type="submit" 
                    data-ecl-language-list-close>
                <span class="ecl-button__container">
                    <span class="ecl-button__label">Close</span>
                    <svg class="ecl-icon ecl-icon--m ecl-button__icon">
                        <use href="/ecl-assets/icons/icons.svg#close"></use>
                    </svg>
                </span>
            </button>
        </div>
        <div class="ecl-site-header__language-content" data-ecl-language-list-content>
            <div class="ecl-site-header__language-category">
                <div class="ecl-site-header__language-category-title">
                    Official EU languages:
                </div>
                <ul class="ecl-site-header__language-list">
                    <li class="ecl-site-header__language-item">
                        <a href="#" class="ecl-link ecl-site-header__language-link ecl-site-header__language-link--active">
                            <span class="ecl-site-header__language-link-code">en</span>
                            <span class="ecl-site-header__language-link-label">English</span>
                        </a>
                    </li>
                    <li class="ecl-site-header__language-item">
                        <a href="#" class="ecl-link ecl-site-header__language-link">
                            <span class="ecl-site-header__language-link-code">fr</span>
                            <span class="ecl-site-header__language-link-label">français</span>
                        </a>
                    </li>
                    <li class="ecl-site-header__language-item">
                        <a href="#" class="ecl-link ecl-site-header__language-link">
                            <span class="ecl-site-header__language-link-code">de</span>
                            <span class="ecl-site-header__language-link-label">Deutsch</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
```

### 2. Accordion - Icon Structure Critical

**WRONG** - Icons not wrapped, wrong size, extra classes:
```html
<span class="ecl-accordion__toggle-flex">
    <span class="ecl-accordion__toggle-title">Item Title</span>
    <!-- WRONG: Icons directly in flex, --m size, extra --primary class -->
    <svg class="ecl-icon ecl-icon--m ecl-accordion__toggle-icon ecl-accordion__toggle-icon--primary">
        <use href="/ecl-assets/icons/icons.svg#plus"></use>
    </svg>
    <svg class="ecl-icon ecl-icon--m ecl-accordion__toggle-icon ecl-accordion__toggle-icon--primary">
        <use href="/ecl-assets/icons/icons.svg#minus"></use>
    </svg>
</span>
```

**CORRECT** - Icons wrapped in indicator span, --s size:
```html
<span class="ecl-accordion__toggle-flex">
    <span class="ecl-accordion__toggle-title">Item Title</span>
    <!-- CORRECT: Icons wrapped in toggle-indicator, --s size -->
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
```

## File Structure

**Isolated ECL Ecosystem:**
```
resources/views/ecl.blade.php          # Blade template with ECL assets
resources/js/ecl.ts                    # Isolated Inertia entry point
resources/js/layouts/EclLayout.vue     # Layout component (components don't initialize!)
resources/js/components/ecl/
    ├── EclHeader.vue                  # Header component (doesn't initialize!)
    └── EclFooter.vue                  # Footer component
resources/js/pages/EclDemo.vue         # Page component (DOES initialize!)
```

## Key Lessons Learned

1. **Always follow ECL examples EXACTLY** - Don't simplify or modify the structure
2. **Icon sizing matters** - Use `--s` for accordion icons, not `--m`
3. **Wrapper elements are required** - The `toggle-indicator` span is not optional
4. **Complete structures required** - Language selector needs the full overlay with language list
5. **Layout vs. Page components** - ECL.autoInit() timing differs for layout vs. page components
6. **Test with simple components first** - Accordion is a good diagnostic tool
7. **Use ECL MCP tools** - Always retrieve official examples, don't guess
8. **Asset paths critical** - Use `/ecl-assets/` prefix for local assets
9. **Inertia navigation needs handling** - Add listener for `inertia:navigate` event

## Unresolved Issue

**Layout component initialization still broken.** The ECL JavaScript works perfectly for components in page-level Vue files but fails for components in layout-level Vue files. 

**Potential solutions to explore:**
1. Use Vue's `onMounted()` hook to manually call `ECL.autoInit()` after layout components mount
2. Create a Vue composable that initializes ECL on mount: `useEclInit()`
3. Use Vue's `nextTick()` to ensure DOM is ready before initialization
4. Consider if layout components need to be regular HTML in the blade template instead of Vue components
5. Manually initialize specific components: `new ECL.SiteHeader(element).init()`

## Working Test Case

Add this accordion to any ECL page component to test ECL JavaScript:

```vue
<div class="ecl-accordion" data-ecl-auto-init="Accordion" data-ecl-accordion>
    <div class="ecl-accordion__item">
        <h3 class="ecl-accordion__title" id="test-accordion-title">
            <button type="button" 
                    class="ecl-accordion__toggle" 
                    data-ecl-accordion-toggle>
                <span class="ecl-accordion__toggle-flex">
                    <span class="ecl-accordion__toggle-title">
                        Test Accordion Item
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
        <div class="ecl-accordion__content" hidden>
            If this expands, ECL JavaScript is working!
        </div>
    </div>
</div>
```

If the accordion works but header components don't, the issue is layout component timing.

## Keywords
- ECL
- Europa Component Library
- JavaScript initialization
- Laravel Inertia
- Vue 3
- debugging
- ECL.autoInit
- layout components
- page components
- accordion
- language selector
- site header

## Snippets

### accordion-correct-structure (html)
Correct ECL accordion structure with properly wrapped icons
```html
<div class="ecl-accordion" data-ecl-auto-init="Accordion" data-ecl-accordion>
    <div class="ecl-accordion__item">
        <h3 class="ecl-accordion__title" id="accordion-title-1">
            <button type="button" 
                    class="ecl-accordion__toggle" 
                    data-ecl-accordion-toggle 
                    aria-controls="accordion-content-1">
                <span class="ecl-accordion__toggle-flex">
                    <span class="ecl-accordion__toggle-title">
                        Accordion Item Title
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
             id="accordion-content-1" 
             role="region" 
             aria-labelledby="accordion-title-1">
            <div class="ecl-accordion__body">
                <p class="ecl-u-type-paragraph">
                    Accordion content goes here.
                </p>
            </div>
        </div>
    </div>
</div>
```

### ecl-autoinit-blade (html)
ECL JavaScript initialization with both DOMContentLoaded and Inertia navigation support
```html
<script src="/ecl-assets/js/ecl-ec.js"></script>
<script>
    // Initial page load
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof ECL !== 'undefined' && typeof ECL.autoInit === 'function') {
            ECL.autoInit();
        }
    });

    // Inertia client-side navigation
    document.addEventListener('inertia:navigate', function () {
        setTimeout(function() {
            if (typeof ECL !== 'undefined' && typeof ECL.autoInit === 'function') {
                ECL.autoInit();
            }
        }, 50);
    });
</script>
```

### ecl-layout-usage (vue)
How to use the ECL composable in layout components (potential solution)
```vue
<script setup lang="ts">
import { useEclInit } from '@/composables/useEclInit';
import EclHeader from '@/components/ecl/EclHeader.vue';
import EclFooter from '@/components/ecl/EclFooter.vue';

// Auto-initialize ECL components when layout mounts
useEclInit();
</script>

<template>
    <EclHeader />
    <slot />
    <EclFooter />
</template>
```

### language-selector-complete (html)
Complete ECL language selector with overlay and language options
```html
<div class="ecl-site-header__language">
    <a class="ecl-button ecl-button--tertiary ecl-site-header__language-selector" 
       href="#" 
       data-ecl-language-selector 
       role="button" 
       aria-label="Change language, current language is English - EN" 
       aria-controls="language-list-overlay">
        <span class="ecl-site-header__language-icon">
            <svg class="ecl-icon ecl-icon--s ecl-site-header__icon" focusable="false" aria-hidden="false" role="img">
                <title>EN</title>
                <use href="/ecl-assets/icons/icons.svg#global"></use>
            </svg>
        </span>
        EN
    </a>
    <div class="ecl-site-header__language-container" 
         id="language-list-overlay" 
         hidden 
         data-ecl-language-list-overlay 
         aria-labelledby="ecl-site-header__language-title" 
         role="dialog">
        <div class="ecl-site-header__language-header">
            <div class="ecl-site-header__language-title" id="ecl-site-header__language-title">
                Select your language
            </div>
            <button class="ecl-button ecl-button--tertiary ecl-site-header__language-close ecl-button--icon-only" 
                    type="submit" 
                    data-ecl-language-list-close>
                <span class="ecl-button__container">
                    <span class="ecl-button__label" data-ecl-label="true">Close</span>
                    <svg class="ecl-icon ecl-icon--m ecl-button__icon" focusable="false" aria-hidden="true" data-ecl-icon>
                        <use href="/ecl-assets/icons/icons.svg#close"></use>
                    </svg>
                </span>
            </button>
        </div>
        <div class="ecl-site-header__language-content" data-ecl-language-list-content>
            <div class="ecl-site-header__language-category" data-ecl-language-list-eu>
                <div class="ecl-site-header__language-category-title">Official EU languages:</div>
                <ul class="ecl-site-header__language-list">
                    <li class="ecl-site-header__language-item">
                        <a href="#" 
                           class="ecl-link ecl-link--standalone ecl-link--no-visited ecl-site-header__language-link ecl-site-header__language-link--active" 
                           hreflang="en">
                            <span class="ecl-site-header__language-link-code">en</span>
                            <span class="ecl-site-header__language-link-label" lang="en">English</span>
                        </a>
                    </li>
                    <li class="ecl-site-header__language-item">
                        <a href="#" 
                           class="ecl-link ecl-link--standalone ecl-link--no-visited ecl-site-header__language-link" 
                           hreflang="fr">
                            <span class="ecl-site-header__language-link-code">fr</span>
                            <span class="ecl-site-header__language-link-label" lang="fr">français</span>
                        </a>
                    </li>
                    <li class="ecl-site-header__language-item">
                        <a href="#" 
                           class="ecl-link ecl-link--standalone ecl-link--no-visited ecl-site-header__language-link" 
                           hreflang="de">
                            <span class="ecl-site-header__language-link-code">de</span>
                            <span class="ecl-site-header__language-link-label" lang="de">Deutsch</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
```

### vue-composable-ecl-init (typescript)
Potential Vue composable solution for ECL initialization in layout components
```typescript
import { onMounted, nextTick } from 'vue';

export function useEclInit() {
    onMounted(async () => {
        await nextTick();
        
        // Give DOM extra time to fully render
        setTimeout(() => {
            if (typeof window !== 'undefined' && 
                typeof (window as any).ECL !== 'undefined' && 
                typeof (window as any).ECL.autoInit === 'function') {
                (window as any).ECL.autoInit();
            }
        }, 100);
    });
}
```

## Usage
All 5 snippet(s) are included above with full code. You can reference them by their 'ref' field.

## Next Actions
- Add another snippet: Use 'recipe_add_snippet' tool with recipe_id=4, ref=<unique_ref>, snippet=<code>, language=<language>, description=<description>
- Add an addendum: Use 'update_recipe' tool with id=4, addendum=<additional_notes_or_updates>