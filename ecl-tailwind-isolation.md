# Integrating ECL (European Commission Component Library) with Complete CSS/JS Isolation in Laravel + Inertia + Vue

## Description
Complete guide for integrating European Commission Component Library (ECL) into Laravel + Inertia.js + Vue.js applications with complete CSS/JS isolation

## Content

This recipe demonstrates how to integrate the European Commission Component Library (ECL) into a Laravel + Inertia.js + Vue.js application while maintaining complete CSS and JavaScript isolation from the main application.

The key challenge with ECL integration is that it includes a CSS reset that conflicts with modern frameworks like Tailwind CSS. This recipe solves this by creating a completely separate ecosystem for ECL pages using Laravel's root view feature.

## Key Benefits
- **Complete Isolation**: ECL pages use their own CSS/JS ecosystem, separate from Tailwind/main app styles
- **No Conflicts**: ECL's CSS reset won't interfere with your main app's styling
- **Clean Architecture**: ECL components are properly scoped and don't leak into other pages
- **Maintainable**: Easy to update ECL version without affecting main app

## Prerequisites
- Laravel application with Inertia.js and Vue.js
- ECL assets downloaded and placed in `public/ecl-assets/`
- Basic understanding of Laravel routing and Vue components

## Step-by-Step Implementation

### 1. Download ECL Assets
First, download the ECL assets and place them in your public directory with the proper structure (see snippet asset_structure).

### 2. Create Separate Root View for ECL
Create `resources/views/ecl.blade.php` for ECL pages (see snippet ecl_root_view). This view loads only ECL assets and uses a separate JavaScript entry point.

### 3. Create ECL Entry Point
Create `resources/js/ecl.ts` for ECL-specific JavaScript (see snippet ecl_entry_point). This keeps ECL JavaScript completely separate from your main app.

### 4. Create ECL Layout Component
Create `resources/js/layouts/EclLayout.vue` (see snippet ecl_layout). This provides the basic structure for ECL pages.

### 5. Create ECL Page Components
Create ECL demo pages like `resources/js/pages/EclDemo.vue` that use the ECL layout (see snippet ecl_page_example).

### 6. Configure Routes
Update `routes/web.php` to use the ECL root view for ECL pages (see snippet ecl_routes). The `->rootView('ecl')` is crucial for isolation.

### 7. Update Vite Configuration
Ensure `vite.config.ts` includes both entry points (see snippet vite_config).

### 8. Build and Test
Build the application and test both ecosystems:

```bash
npm run build
```

- **Main app pages**: Should have Tailwind CSS and dark mode
- **ECL pages**: Should have pure ECL styling with no main app interference

## Troubleshooting

### ECL Components Not Working
- Ensure ECL JS is loaded and `ECL.autoInit()` is called
- Check that ECL CSS files are accessible at the correct paths
- Verify that components have proper `data-ecl-*` attributes

### CSS Conflicts
- ECL pages should use `->rootView('ecl')` to ensure isolation
- Main app pages should NOT load ECL CSS
- Check browser dev tools for CSS conflicts

### Navigation Issues
- Use Inertia `<Link>` components for SPA navigation within ECL pages
- ECL breadcrumb links should also use Inertia links for consistency

## Best Practices

1. **Keep Ecosystems Separate**: Never mix ECL and main app CSS in the same page
2. **Use ECL Components Properly**: Follow ECL documentation for component usage
3. **Accessibility**: ECL components are WCAG 2.1 AA compliant by default
4. **Version Management**: Keep ECL assets updated and test thoroughly after updates
5. **Performance**: ECL pages load only ECL assets, keeping main app bundle size down

## Keywords
laravel, inertia, vue, ecl, european-commission, css-isolation, component-library, accessibility

## Code Snippets

### asset_structure
**Language:** text  
**Description:** ECL assets directory structure in public folder

```
public/ecl-assets/
├── css/
│   ├── ecl-reset.css
│   ├── ecl-ec.css
│   ├── ecl-ec-utilities.css
│   └── ecl-ec-print.css
├── js/
│   └── ecl-ec.js
└── icons/
    └── icons.svg
```

### ecl_root_view
**Language:** blade  
**Description:** ECL root view template with isolated CSS/JS loading

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    
    <title inertia>{{ config('app.name', 'Laravel') }}</title>
    
    <script>
        document.documentElement.classList.remove('no-js');
        document.documentElement.classList.add('has-js');
    </script>
    
    <!-- ECL CSS only - no main app CSS -->
    <link rel="stylesheet" href="{{ asset('ecl-assets/css/ecl-reset.css') }}">
    <link rel="stylesheet" href="{{ asset('ecl-assets/css/ecl-ec.css') }}">
    <link rel="stylesheet" href="{{ asset('ecl-assets/css/ecl-ec-utilities.css') }}">
    <link rel="stylesheet" href="{{ asset('ecl-assets/css/ecl-ec-print.css') }}" media="print">
    
    @vite(['resources/js/ecl.ts', "resources/js/pages/{$page['component']}.vue"])
    @inertiaHead
</head>
<body>
    @inertia
    
    <!-- ECL JavaScript -->
    <script src="{{ asset('ecl-assets/js/ecl-ec.js') }}"></script>
    <script>
        // Initialize ECL on page load
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof ECL !== 'undefined' && ECL.autoInit) {
                ECL.autoInit();
            }
        });
        
        // Reinitialize ECL on Inertia navigation
        document.addEventListener('inertia:navigate', function() {
            if (typeof ECL !== 'undefined' && ECL.autoInit) {
                setTimeout(function() {
                    ECL.autoInit();
                }, 50);
            }
        });
    </script>
</body>
</html>
```

### ecl_entry_point
**Language:** typescript  
**Description:** ECL entry point for isolated JavaScript ecosystem

```typescript
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })\n            .use(plugin)\n            .mount(el);
    },
    progress: {
        color: '#004494', // ECL blue
    },
});
```

### ecl_layout
**Language:** vue  
**Description:** ECL layout component with header and footer

```vue
<script setup lang="ts">
import EclHeader from '@/components/ecl/EclHeader.vue';
import EclFooter from '@/components/ecl/EclFooter.vue';
</script>

<template>
    <EclHeader />
    
    <!-- Page Content -->
    <slot />
    
    <EclFooter />
</template>
```

### ecl_page_example
**Language:** vue  
**Description:** Example ECL page component using the ECL layout

```vue
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
    
    <!-- ECL Page Header -->
    <div class="ecl-page-header">
        <div class="ecl-container">
            <!-- ECL breadcrumb and page title -->
        </div>
    </div>
    
    <!-- ECL Content -->
    <main class="ecl-u-pv-xl">
        <div class="ecl-container">
            <!-- ECL components and content -->
        </div>
    </main>
</template>
```

### ecl_routes
**Language:** php  
**Description:** Laravel routes configuration for ECL pages with isolated root view

```php
Route::get('ecl-demo', function () {
    return Inertia::render('EclDemo')->rootView('ecl');
})->name('ecl-demo');

Route::get('ecl-table-demo', function () {
    return Inertia::render('EclTableDemo')->rootView('ecl');
})->name('ecl-table-demo');
```

### vite_config
**Language:** typescript  
**Description:** Vite configuration with both main app and ECL entry points

```typescript
export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.ts',     // Main app
                'resources/js/ecl.ts',     // ECL app
                'resources/css/app.css',
            ],
            refresh: true,
        }),
        vue(),
    ],
});
```

## Metadata
- **Created:** 2025-11-27 18:07:42
- **Recipe ID:** 6