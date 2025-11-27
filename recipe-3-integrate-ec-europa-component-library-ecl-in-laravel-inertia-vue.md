# Recipe 3: Integrate EC Europa Component Library (ECL) in Laravel Inertia Vue

## Description
Complete guide to integrating ECL v4 design system into Laravel+Inertia+Vue project using isolated ecosystem architecture. Covers setup, asset management, proper component structure, and critical troubleshooting for blank pages, JavaScript errors, and missing assets.

## Content

# Complete ECL Integration in Laravel Inertia Vue

This recipe documents the complete process of integrating the EC Europa Component Library (ECL) v4 into a Laravel + Inertia.js + Vue 3 + TypeScript project using an isolated ecosystem architecture.

## Prerequisites

- Laravel 11+ with Inertia.js and Vue 3 TypeScript
- Vite build system
- Laravel Herd or similar local development environment
- ECL MCP Server (for documentation and templates)

## Architecture Overview

The ECL integration uses a **completely isolated ecosystem** separate from your main application:

1. **Separate Blade Template** (`ecl.blade.php`) - Loads ECL-specific assets
2. **Separate TypeScript Entry** (`ecl.ts`) - No CSS imports, no Ziggy props access
3. **Separate Layout Component** (`EclLayout.vue`) - Contains full ECL HTML structure
4. **Page Components** - Contain only page-specific content with ECL classes

### Critical Architecture Principles

- **Layouts contain structure** (header, footer, navigation)
- **Pages contain content** (page headers, main sections)
- **Use static paths only** (`/ecl-assets/...`) NOT dynamic Ziggy paths
- **Follow exact ECL templates** - don't modify or simplify the HTML structure

## Step 1: Download ECL Assets

Use the ECL MCP server to get the starter template and understand the required assets:

```bash
# Use mcp_ecl_start_here to understand the workflow
# Use mcp_ecl_get_starter_template to get the exact HTML structure
```

Create a download script for ECL v4.11.1 assets (see snippet `download-script`).

Download all ECL assets including:
- CSS files (reset, main, utilities, print)
- JavaScript files (ecl-ec.js)
- Icon sprites (icons.svg, icons-social-media.svg, icons-flag.svg)
- **Both logo variants**: positive (for light backgrounds) AND negative (for dark backgrounds)

```bash
chmod +x download-ecl-assets.sh
./download-ecl-assets.sh
```

**CRITICAL**: Ensure you download BOTH logo variants:
- `/ecl-assets/images/logo/logo-ec--en.svg` (positive/regular for header)
- `/ecl-assets/images/logo/negative/logo-ec--en.svg` (white for footer dark background)

## Step 2: Create Isolated Blade Template

Create `resources/views/ecl.blade.php` that loads ECL assets but NOT your main app CSS (see snippet `ecl-blade`).

Key features:
- Loads 4 ECL CSS files
- Includes `@vite('resources/js/ecl.ts')` for isolated entry point
- Loads ECL JavaScript with `ECL.autoInit()` on DOMContentLoaded
- Uses `no-js` class detection
- Contains `@inertia` directive for Inertia mount point

## Step 3: Create Isolated TypeScript Entry

Create `resources/js/ecl.ts` (see snippet `ecl-entry`).

**CRITICAL**: This entry point is isolated and does NOT have access to:
- Ziggy route props (`page.props.ziggy.url`)
- Main app state or composables
- Main app CSS

## Step 4: Configure Vite Multi-Entry

Update `vite.config.ts` to include both entry points (see snippet `vite-config`).

Run build to verify:
```bash
npm run build
```

## Step 5: Create ECL Layout Component

Create `resources/js/layouts/EclLayout.vue` with the EXACT structure from ECL starter template (see snippet `ecl-layout`).

**CRITICAL POINTS**:

1. **Use exact ECL template structure** - Don't modify or simplify
   - Get it from `mcp_ecl_get_starter_template` tool
   - Full site header with language selector, search form, banner, menu
   - Full site footer with split-columns layout, logo sections, link sections

2. **Use static asset paths only**:
   ```vue
   <!-- CORRECT -->
   <img src="/ecl-assets/images/logo/logo-ec--en.svg" alt="..." />
   
   <!-- WRONG - Will cause JavaScript error -->
   <img :src="`${page.props.ziggy.url}/ecl-assets/...`" alt="..." />
   ```

3. **No Vue imports needed** - Just use static HTML with ECL classes

4. **Include all `data-ecl-auto-init` attributes** for JavaScript component initialization

5. **Use negative logo in footer**:
   ```vue
   <!-- Footer needs white logo for dark background -->
   <img src="/ecl-assets/images/logo/negative/logo-ec--en.svg" alt="..." />
   ```

## Step 6: Create ECL Page Component

Create `resources/js/pages/EclDemo.vue` with ONLY page content (see snippet `ecl-page`).

Page should contain:
- Inertia Head component for title
- Page header with breadcrumbs (optional)
- Main content area with ECL utility classes
- NO header or footer (those are in layout)

## Step 7: Configure Route

Add route in `routes/web.php` (see snippet `route-config`).

**CRITICAL**: Use `->rootView('ecl')` to override default blade template.

## Step 8: Build and Test

```bash
npm run build
```

Visit the page (e.g., `https://sandbox.test/ecl-demo`).

## Common Issues and Solutions

### Issue 1: Blank White Page

**Symptom**: Page loads but shows nothing, no errors in console.

**Cause**: Architectural mistake - putting header/footer in page component instead of layout.

**Solution**: 
- Layouts contain persistent structure (header, footer, navigation)
- Pages contain variable content (page headers, main sections)
- Move header/footer from page to layout

### Issue 2: JavaScript Error - "undefined is not an object (evaluating 'i(a).props.ziggy.url')"

**Symptom**: TypeError in browser console mentioning ziggy.url.

**Cause**: Using `usePage()` or dynamic Ziggy paths in isolated ecosystem that doesn't have Ziggy props.

**Solution**:
1. Remove `usePage()` import from layout/page components
2. Change all dynamic paths to static paths:
   ```vue
   <!-- Change this -->
   <script setup>
   const page = usePage();
   </script>
   <img :src="`${page.props.ziggy.url}/ecl-assets/...`" />
   
   <!-- To this -->
   <img src="/ecl-assets/images/logo/logo-ec--en.svg" />
   ```

### Issue 3: Header/Footer Don't Match ECL Design

**Symptom**: Structure looks simplified or modified.

**Cause**: Modifying or simplifying the ECL template structure.

**Solution**:
- Always use `mcp_ecl_get_starter_template` to get exact HTML
- Copy the COMPLETE header and footer structure
- Don't remove or simplify any elements
- ECL is a mature framework (300+ contributors) - use exact templates

### Issue 4: Footer Logo Not Showing

**Symptom**: White space where footer logo should be.

**Cause**: Missing negative logo variant for dark footer background.

**Solution**:
- ECL uses two logo variants:
  - **Positive** (regular colors) for light backgrounds
  - **Negative** (white) for dark backgrounds
- Ensure both are downloaded:
  ```bash
  # Download negative logo
  curl -o public/ecl-assets/images/logo/negative/logo-ec--en.svg \
    https://cdn1.fpfis.tech.ec.europa.eu/ecl/v4.11.1/ec/images/logo/negative/logo-ec--en.svg
  ```

## Best Practices

1. **Always use ECL MCP server** for documentation and examples
   - `mcp_ecl_search_documentation_pages` for component docs
   - `mcp_ecl_search_examples` for HTML patterns
   - `mcp_ecl_get_starter_template` for base structure

2. **Never modify ECL HTML structure** - use exact templates

3. **Use static paths exclusively** in isolated ecosystem

4. **Verify all assets exist** before deploying:
   - Check both logo variants (positive and negative)
   - Verify all icon sprites
   - Confirm CSS and JS files

5. **Follow ECL naming conventions** for classes and data attributes

6. **Test in browser** after each major change:
   - Check browser console for errors
   - Verify all assets load (Network tab)
   - Confirm ECL JavaScript initializes properly

## Adding More ECL Pages

To add additional ECL pages:

1. Create new page component in `resources/js/pages/`
2. Use existing `EclLayout` or create variant
3. Add route with `->rootView('ecl')`
4. Use ECL MCP server to find component HTML
5. Always use static paths
6. Build and test

## Resources

- ECL Documentation: https://ec.europa.eu/component-library/
- ECL CDN: https://cdn1.fpfis.tech.ec.europa.eu/ecl/v4.11.1/ec/
- ECL GitHub: https://github.com/ec-europa/europa-component-library
- Use ECL MCP server for version-specific guidance

## Keywords
- ECL
- Europa Component Library
- Laravel
- Inertia
- Vue 3
- TypeScript
- isolated ecosystem
- Vite multi-entry
- design system
- EU design
- static assets
- Ziggy routes
- architectural patterns

## Snippets

### download-script (bash)
Shell script to download all ECL v4.11.1 assets from CDN
```bash
#!/bin/bash

# ECL Asset Download Script for v4.11.1
ECL_VERSION="4.11.1"
BASE_URL="https://cdn1.fpfis.tech.ec.europa.eu/ecl/v${ECL_VERSION}/ec"
OUTPUT_DIR="public/ecl-assets"

echo "Downloading ECL v${ECL_VERSION} assets..."

# Create directory structure
mkdir -p "${OUTPUT_DIR}/css"
mkdir -p "${OUTPUT_DIR}/js"
mkdir -p "${OUTPUT_DIR}/icons"
mkdir -p "${OUTPUT_DIR}/images/logo"
mkdir -p "${OUTPUT_DIR}/images/logo/negative"

# Download CSS files
curl -o "${OUTPUT_DIR}/css/ecl-reset.css" "${BASE_URL}/styles/ecl-reset.css"
curl -o "${OUTPUT_DIR}/css/ecl-ec.css" "${BASE_URL}/styles/ecl-ec.css"
curl -o "${OUTPUT_DIR}/css/ecl-ec-utilities.css" "${BASE_URL}/styles/ecl-ec-utilities.css"
curl -o "${OUTPUT_DIR}/css/ecl-ec-print.css" "${BASE_URL}/styles/ecl-ec-print.css"

# Download JavaScript
curl -o "${OUTPUT_DIR}/js/ecl-ec.js" "${BASE_URL}/scripts/ecl-ec.js"

# Download icon sprites
curl -o "${OUTPUT_DIR}/icons/icons.svg" "${BASE_URL}/images/icons/sprites/icons.svg"
curl -o "${OUTPUT_DIR}/icons/icons-social-media.svg" "${BASE_URL}/images/icons/sprites/icons-social-media.svg"
curl -o "${OUTPUT_DIR}/icons/icons-flag.svg" "${BASE_URL}/images/icons/sprites/icons-flag.svg"

# Download logos - BOTH variants
curl -o "${OUTPUT_DIR}/images/logo/logo-ec--en.svg" "${BASE_URL}/images/logo/logo-ec--en.svg"
curl -o "${OUTPUT_DIR}/images/logo/negative/logo-ec--en.svg" "${BASE_URL}/images/logo/negative/logo-ec--en.svg"

echo "ECL assets downloaded successfully!"
```

### ecl-blade (blade)
Isolated Blade template for ECL pages with asset loading
```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    <!-- ECL CSS Assets -->
    <link rel="stylesheet" href="/ecl-assets/css/ecl-reset.css">
    <link rel="stylesheet" href="/ecl-assets/css/ecl-ec.css">
    <link rel="stylesheet" href="/ecl-assets/css/ecl-ec-utilities.css">
    <link rel="stylesheet" href="/ecl-assets/css/ecl-ec-print.css" media="print">

    <!-- Vite ECL Entry Point (NO main app CSS) -->
    @vite('resources/js/ecl.ts')
    @inertiaHead

    <!-- No-JS Detection -->
    <script>
        document.documentElement.classList.remove('no-js');
    </script>
</head>
<body>
    @inertia

    <!-- ECL JavaScript with Auto-Init -->
    <script src="/ecl-assets/js/ecl-ec.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof ECL !== 'undefined' && ECL.autoInit) {
                ECL.autoInit();
            }
        });
    </script>
</body>
</html>
```

### ecl-entry (typescript)
Isolated TypeScript entry point for ECL ecosystem (no CSS, no Ziggy access)
```typescript
import { createApp, h, DefineComponent } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';

// NO CSS IMPORTS - ECL CSS loaded in blade template
// NO Ziggy access - isolated ecosystem

createInertiaApp({
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
});
```

### ecl-layout (vue)
ECL Layout with exact starter template structure (header, slot, footer)
```vue
<script setup lang="ts">
// No imports needed - using static asset paths only
</script>

<template>
    <!-- Site Header - EXACT structure from mcp_ecl_get_starter_template -->
    <header class="ecl-site-header ecl-site-header-with-logo-l ecl-site-header--has-menu" data-ecl-auto-init="SiteHeader">
        <div class="ecl-site-header__inner">
            <div class="ecl-site-header__background">
                <div class="ecl-site-header__header">
                    <div class="ecl-site-header__container ecl-container">
                        <div class="ecl-site-header__top" data-ecl-site-header-top>
                            <!-- Logo with STATIC path -->
                            <a href="/" class="ecl-link ecl-link--standalone ecl-site-header__logo-link">
                                <img class="ecl-site-header__logo-image ecl-site-header__logo-image--l" 
                                     src="/ecl-assets/images/logo/logo-ec--en.svg" 
                                     alt="European Commission" />
                            </a>
                            <!-- Language selector, search form, etc. -->
                            <div class="ecl-site-header__action">
                                <!-- Language selector with static icon path -->
                                <div class="ecl-site-header__language">
                                    <a class="ecl-button ecl-button--tertiary ecl-site-header__language-selector" 
                                       href="#" 
                                       data-ecl-language-selector>
                                        <svg class="ecl-icon ecl-icon--s" focusable="false" aria-hidden="false">
                                            <use href="/ecl-assets/icons/icons.svg#global"></use>
                                        </svg>
                                        EN
                                    </a>
                                </div>
                                <!-- Search form, etc. -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Site banner -->
        <div class="ecl-site-header__banner">
            <div class="ecl-container">
                <div class="ecl-site-header__site-name">Your Site Name</div>
            </div>
        </div>
        <!-- Navigation menu -->
        <nav class="ecl-menu" data-ecl-menu data-ecl-auto-init="Menu">
            <!-- Full menu structure -->
        </nav>
    </header>

    <!-- Page Content Slot -->
    <slot />

    <!-- Site Footer - EXACT structure from ECL starter template -->
    <footer class="ecl-site-footer ecl-site-footer--split-columns">
        <div class="ecl-container ecl-site-footer__container">
            <div class="ecl-site-footer__row">
                <div class="ecl-site-footer__column">
                    <!-- Logo section with NEGATIVE variant for dark background -->
                    <div class="ecl-site-footer__section">
                        <a href="/" class="ecl-link ecl-link--standalone ecl-site-footer__logo-link">
                            <img class="ecl-site-footer__logo-image" 
                                 src="/ecl-assets/images/logo/negative/logo-ec--en.svg" 
                                 alt="European Commission" />
                        </a>
                        <div class="ecl-site-footer__description">
                            This site is managed by: Your Organization
                        </div>
                    </div>
                </div>
                <div class="ecl-site-footer__column">
                    <!-- Footer links sections -->
                    <div class="ecl-site-footer__section">
                        <ul class="ecl-site-footer__list">
                            <li class="ecl-site-footer__list-item">
                                <a href="#" class="ecl-link ecl-link--standalone ecl-link--inverted ecl-site-footer__link">
                                    Footer Link
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>
```

### ecl-page (vue)
ECL page component with only content (no header/footer)
```vue
<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

defineOptions({
    layout: (h: any, page: any) => {
        const EclLayout = defineAsyncComponent(() =>
            import('@/layouts/EclLayout.vue')
        );
        return h(EclLayout, () => page);
    },
});
</script>

<template>
    <Head title="ECL Demo" />

    <!-- Page Header with Breadcrumbs (Optional) -->
    <div class="ecl-page-header">
        <div class="ecl-container">
            <nav class="ecl-page-header__breadcrumb" aria-label="Breadcrumb">
                <ol class="ecl-breadcrumb ecl-page-header__breadcrumb-list">
                    <li class="ecl-breadcrumb__item">
                        <a href="/" class="ecl-link ecl-link--standalone ecl-breadcrumb__link">Home</a>
                    </li>
                    <li class="ecl-breadcrumb__item">
                        <span class="ecl-breadcrumb__current">ECL Demo</span>
                    </li>
                </ol>
            </nav>
            <h1 class="ecl-page-header__title ecl-u-type-heading-1">ECL Demo Page</h1>
        </div>
    </div>

    <!-- Main Content Area -->
    <main class="ecl-u-pv-xl">
        <div class="ecl-container">
            <p class="ecl-u-type-paragraph">
                Your page content using ECL utility classes.
            </p>
        </div>
    </main>
</template>
```

### route-config (php)
Laravel route configuration for ECL page with rootView override
```php
// routes/web.php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ECL Demo Route - Uses isolated ECL ecosystem
Route::get('ecl-demo', function () {
    return Inertia::render('EclDemo')
        ->rootView('ecl');  // Override default blade template
})->name('ecl-demo');
```

### vite-config (typescript)
Vite configuration with multi-entry setup for main app and ECL
```typescript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.ts',      // Main application
                'resources/js/ecl.ts',      // Isolated ECL ecosystem
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
```

## Usage
All 7 snippet(s) are included above with full code. You can reference them by their 'ref' field.

## Next Actions
- Add another snippet: Use 'recipe_add_snippet' tool with recipe_id=3, ref=<unique_ref>, snippet=<code>, language=<language>, description=<description>
- Add an addendum: Use 'update_recipe' tool with id=3, addendum=<additional_notes_or_updates>