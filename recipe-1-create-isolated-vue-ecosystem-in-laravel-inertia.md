# Create Isolated Vue Ecosystem in Laravel Inertia

**Recipe ID:** 1
**Title:** Create Isolated Vue Ecosystem in Laravel Inertia
**Description:** Build a completely CSS-free isolated Vue page ecosystem in an existing Laravel + Vue + Inertia + Tailwind application, bypassing all global styles while maintaining proper Inertia architecture
**Created:** 2025-11-27 14:03:05

## Content
This recipe shows how to create a completely isolated Vue/Inertia page that loads ZERO CSS from your main application. Perfect for creating raw HTML pages, testing browser defaults, or building standalone features that shouldn't inherit your app's styling.

## The Problem
In a typical Laravel + Inertia + Vue + Tailwind setup, all pages load from the same `app.blade.php` template and `app.ts` entry point, which include global CSS, Tailwind, dark mode classes, and theme initialization. You can't just \"skip\" CSS for one page because it's all bundled together.

## The Solution: The \"Blanco\" Ecosystem
Create a parallel, isolated ecosystem with its own blade template, TypeScript entry point, and minimal layout - completely separate from your main app's styling system.

### Step 1: Create Isolated Blade Template
Create a raw HTML blade template (see snippet blanco_blade) at `resources/views/blanco.blade.php`. This is KEY - no dark mode classes on `<html>`, no CSS imports, no fonts, no inline styles. Just the bare minimum to load Inertia and your isolated entry point.

### Step 2: Create Isolated TypeScript Entry
Create a separate Inertia app entry (see snippet blanco_ts) at `resources/js/blanco.ts`. This mirrors your main `app.ts` structure but critically EXCLUDES the CSS import and theme initialization. This is what prevents all styling from loading.

### Step 3: Update Vite Config
Add your new entry point to Vite (see snippet vite_config). The Laravel Vite plugin needs to know about both entry points so it can build separate bundles.

### Step 4: Create Minimal Layout
Create a pass-through layout (see snippet blanco_layout) at `resources/js/layouts/BlancoLayout.vue`. This layout does nothing except render its children - no wrappers, no navigation, no styling.

### Step 5: Create Your Page Component
Create your actual page component (see snippet lorem_page) that uses the blanco layout. Use `defineOptions({ layout: BlancoLayout })` to specify the layout.

### Step 6: Define Route with Custom Root View
Use Inertia's `->rootView('blanco')` method (see snippet route_definition) to tell this specific route to use your isolated blade template instead of the default `app.blade.php`.

### Step 7: Build Assets
Run `npm run build` (see snippet build_command) to compile everything. Vite will create separate bundles for your blanco ecosystem.

## Why This Works
- **Separate Blade Template**: Bypasses all HTML-level dark mode classes and inline scripts
- **Separate Entry Point**: Prevents CSS imports and theme initialization that happen in main app.ts
- **Separate Layout**: Avoids inheriting any layout-level styling or structure
- **Vite Multi-Entry**: Creates isolated JavaScript bundles that don't include main app dependencies
- **Root View Override**: Inertia's `->rootView()` method lets you use different blade templates per route

## Common Gotchas
1. **Forgotten Vite Config**: If you get \"Unable to locate file in Vite manifest\" error, you forgot to add the entry to `vite.config.ts`
2. **Still Seeing Styles**: Check that your blanco.blade.php has NO class attributes on `<html>` or `<body>`, and your blanco.ts doesn't import any CSS files
3. **Layout Not Applied**: Make sure you're using `defineOptions({ layout: BlancoLayout })` in your page component, not trying to wrap it manually

## When To Use This
- Creating truly unstyled pages for browser default testing
- Building standalone features that shouldn't inherit app styling
- Generating print-friendly or accessibility-focused views
- Creating pages for embedding in iframes where external styles would conflict

## Keywords
- laravel
- inertia
- vue
- tailwind
- isolated
- css-free
- raw html
- vite
- blade template
- typescript
- layout
- rootView
- multi-entry

## Snippets

### blanco_blade (HTML)
**Description:** Raw HTML blade template with NO CSS, dark mode classes, or styling - only loads blanco.ts entry point
```html
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        @vite(['resources/js/blanco.ts', "resources/js/pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body>
        @inertia
    </body>
</html>
```

### blanco_layout (Vue)
**Description:** Minimal pass-through layout component that only renders children with no wrappers or styling
```vue
<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';

const page = usePage();
</script>

<template>
    <slot />
</template>
```

### blanco_ts (TypeScript)
**Description:** Isolated Inertia app entry point that excludes CSS imports and theme initialization
```typescript
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { type DefineComponent, createApp, h } from 'vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// NOTE: No CSS imports here! No import '../css/app.css'!
// NOTE: No theme initialization! No initializeTheme() call!

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
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
    progress: {
        color: '#4B5563',
    },
});
```

### build_command (Bash)
**Description:** Build command to compile all assets including the isolated blanco ecosystem
```bash
npm run build
```

### lorem_page (Vue)
**Description:** Example page component using BlancoLayout via defineOptions for unstyled HTML rendering
```vue
<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import BlancoLayout from '@/layouts/BlancoLayout.vue';

defineOptions({
    layout: BlancoLayout,
});
</script>

<template>
    <Head title="Lorem Ipsum" />
    
    <h1>Lorem Ipsum</h1>
    
    <h2>What is Lorem Ipsum?</h2>
    <p>
        Lorem Ipsum is simply dummy text of the printing and typesetting industry.
        Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.
    </p>
    
    <h3>Why do we use it?</h3>
    <p>
        It is a long established fact that a reader will be distracted by the readable
        content of a page when looking at its layout.
    </p>
</template>
```

### route_definition (PHP)
**Description:** Route definition using rootView('blanco') to override default blade template
```php
Route::get('lorem-ipsum', function () {
    return Inertia::render('LoremIpsum')->rootView('blanco');
})->name('lorem-ipsum');
```

### vite_config (TypeScript)
**Description:** Vite config with both app.ts and blanco.ts entry points for multi-entry build
```typescript
import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts', 'resources/js/blanco.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        tailwindcss(),
        wayfinder({
            formVariants: true,
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

## Addendums
None

## Usage
All 7 snippet(s) are included above with full code. You can reference them by their 'ref' field.

Use 'recipe_add_snippet' tool to add more code snippets to this recipe

Use 'update_recipe' tool to add an addendum with updates or additional notes

## Next Actions
- Add another snippet: Use 'recipe_add_snippet' tool with recipe_id=1, ref=<unique_ref>, snippet=<code>, language=<language>, description=<description>
- Add an addendum: Use 'update_recipe' tool with id=1, addendum=<additional_notes_or_updates>