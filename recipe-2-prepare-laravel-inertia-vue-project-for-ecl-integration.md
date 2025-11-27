# Prepare Laravel Inertia Vue Project for ECL Integration

**Recipe ID:** 2
**Title:** Prepare Laravel Inertia Vue Project for ECL Integration
**Description:** Set up an isolated ECL ecosystem in a Laravel + Vue + Inertia + Tailwind project, creating a CSS-free foundation ready for EC Europa Component Library integration
**Created:** 2025-11-27 14:07:31

## Content
This recipe prepares your Laravel + Inertia + Vue project for ECL (EC Europa Component Library) integration by creating a completely isolated ecosystem separate from your main application's styling system.

## Why You Need This

The EC Europa Component Library (ECL) provides its own comprehensive CSS framework and component system designed for European Commission websites. Mixing ECL with your existing Tailwind CSS, dark mode, and custom styles will cause conflicts. This recipe creates a parallel \"ECL ecosystem\" that loads ZERO of your application's CSS.

## What You'll Build

A self-contained ECL environment with:
- **Isolated Blade Template**: No dark mode classes, no Tailwind links
- **Separate TypeScript Entry**: Skips your app's CSS imports and theme initialization
- **Minimal Layout Component**: Pass-through wrapper with no styling
- **Multi-Entry Vite Config**: Builds separate bundles for ECL pages
- **Route Override System**: Uses custom blade template via `->rootView('ecl')`

## Step-by-Step Setup

### Step 1: Create ECL Blade Template
Create `resources/views/ecl.blade.php` (see snippet ecl_blade). This is your foundation - it loads ONLY the ECL entry point, not your main app.ts. Critical: No classes on `<html>` or `<body>` tags.

### Step 2: Create ECL TypeScript Entry
Create `resources/js/ecl.ts` (see snippet ecl_ts). This mirrors your main `app.ts` structure but excludes:
- No `import '../css/app.css'`
- No theme initialization functions
- No dark mode setup

This ensures zero CSS from your main app leaks into ECL pages.

### Step 3: Update Vite Configuration
Add `resources/js/ecl.ts` to your Vite inputs array (see snippet vite_config). This tells Vite to create a separate bundle for ECL pages, independent from your main app bundle.

### Step 4: Create ECL Layout Component
Create `resources/js/layouts/EclLayout.vue` (see snippet ecl_layout). This is intentionally minimal - just a `<slot />` renderer. No navigation, no header, no footer, no styling containers. ECL components will provide their own structure.

### Step 5: Create Demo Page
Create `resources/js/pages/EclDemo.vue` (see snippet ecl_demo_page) to verify your setup works. This page uses the ECL layout and will show completely unstyled HTML initially. Once you add ECL assets, this is where you'll test ECL components.

### Step 6: Add Route with Custom Root View
Add a route using Inertia's `->rootView('ecl')` method (see snippet ecl_route). This tells Laravel to use `ecl.blade.php` instead of your default `app.blade.php` for this specific route.

### Step 7: Build and Verify
Run `npm run build` (see snippet build_command) to compile everything. Look for `ecl-[hash].js` and `EclDemo-[hash].js` in your build output to confirm the isolated bundles were created.

Visit `https://your-app.test/ecl-demo` (or your local domain) - you should see completely unstyled HTML with browser default fonts. This confirms zero CSS is loading from your main app.

## What's Next

After completing this recipe, you're ready to:
1. Download ECL assets (CSS, JS, icons, fonts)
2. Add ECL asset references to your `ecl.blade.php`
3. Import ECL's JavaScript in `ecl.ts`
4. Build ECL components in your demo page
5. Create additional ECL pages using the same pattern

## Verification Checklist

✅ Route `/ecl-demo` loads without errors  
✅ Page shows unstyled HTML with browser default fonts  
✅ No Tailwind classes are being applied  
✅ No dark mode is active  
✅ Browser inspector shows only `ecl-[hash].js` loaded, not `app-[hash].js`  
✅ Vite manifest includes separate ECL bundles  

## Common Issues

**\"Unable to locate file in Vite manifest\"**: You forgot to add `resources/js/ecl.ts` to the Vite config's input array.

**Still seeing Tailwind/dark mode**: Check that `ecl.blade.php` has NO class attributes and `ecl.ts` doesn't import any CSS files.

**Layout not applied**: Verify you used `defineOptions({ layout: EclLayout })` in your page component.

**Build errors**: Make sure all file paths are correct and files exist before running build.

## File Structure Summary

```
resources/
├── views/
│   └── ecl.blade.php          (isolated blade template)
├── js/
│   ├── ecl.ts                 (isolated entry point)
│   ├── layouts/
│   │   └── EclLayout.vue      (minimal layout)
│   └── pages/
│       └── EclDemo.vue        (demo page)
routes/
└── web.php                     (route with ->rootView('ecl'))
vite.config.ts                  (multi-entry configuration)
```

This setup maintains proper Laravel + Inertia + Vue architecture while creating a completely isolated styling context perfect for ECL integration.

## Keywords
- ecl
- europa component library
- laravel
- inertia
- vue
- isolated ecosystem
- css-free
- vite
- blade template
- typescript
- preparation

## Snippets

### build_command (Bash)
**Description:** Build command to compile all assets including isolated ECL bundles
```bash
npm run build
```

### ecl_blade (HTML)
**Description:** Isolated blade template for ECL pages - no CSS, no dark mode classes, only loads ecl.ts
```html
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        @vite(['resources/js/ecl.ts', "resources/js/pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body>
        @inertia
    </body>
</html>
```

### ecl_demo_page (Vue)
**Description:** Demo page component using EclLayout to verify isolated ECL ecosystem works correctly
```vue
<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import EclLayout from '@/layouts/EclLayout.vue';

defineOptions({
    layout: EclLayout,
});
</script>

<template>
    <Head title="ECL Demo" />
    
    <div>
        <h1>ECL Demo</h1>
        
        <h2>Setup Complete</h2>
        <p>
            This page is completely isolated from your main application's CSS.
            You should see unstyled HTML with browser default fonts.
        </p>
        
        <h3>Next Steps</h3>
        <p>
            Add ECL assets to ecl.blade.php and start building ECL components here.
        </p>
    </div>
</template>
```

### ecl_layout (Vue)
**Description:** Minimal ECL layout component - just renders children with no wrappers or styling
```vue
<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';

const page = usePage();
</script>

<template>
    <slot />
</template>
```

### ecl_route (PHP)
**Description:** Route definition using rootView('ecl') to override default blade template
```php
Route::get('ecl-demo', function () {
    return Inertia::render('EclDemo')->rootView('ecl');
})->name('ecl-demo');
```

### ecl_ts (TypeScript)
**Description:** Isolated Inertia entry point for ECL - excludes all CSS imports and theme setup from main app
```typescript
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// IMPORTANT: No CSS imports here!
// IMPORTANT: No theme initialization!

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
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

### vite_config (TypeScript)
**Description:** Vite config with multi-entry setup - includes both app.ts and ecl.ts for separate bundles
```typescript
import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            // Add ecl.ts to the input array
            input: ['resources/js/app.ts', 'resources/js/ecl.ts'],
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
- Add another snippet: Use 'recipe_add_snippet' tool with recipe_id=2, ref=<unique_ref>, snippet=<code>, language=<language>, description=<description>
- Add an addendum: Use 'update_recipe' tool with id=2, addendum=<additional_notes_or_updates>