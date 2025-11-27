# Recipe 5: Creating ECL Component Demo Pages in Laravel + Vue

## Description
A recipe for creating demonstration pages for European Commission Library (ECL) components in Laravel + Vue applications

## Content
This recipe demonstrates how to create comprehensive demonstration pages for European Commission Library (ECL) components in a Laravel + Vue + Inertia.js application. The approach focuses on creating well-structured demo pages that showcase component variants, include proper navigation, and follow ECL accessibility standards.

Start by creating the Vue component with proper ECL layout and structure (see snippet ecl_page_template). Add the route to Laravel's web.php file (see snippet add_route). Include navigation links between related demo pages (see snippet navigation_links). Finally, build the assets to generate TypeScript routes (see snippet build_assets).

This pattern is particularly useful for component libraries like ECL where you need to demonstrate multiple variants of components with real-world examples and proper accessibility features.

## Keywords
- ecl
- laravel
- vue
- inertia
- demo
- components
- accessibility
- european-commission

## Snippets

### add_route (php)
Adding a new route for ECL demo pages in Laravel
```php
Route::get('ecl-component-demo', function () {
    return Inertia::render('EclComponentDemo')->rootView('ecl');
})->name('ecl-component-demo');
```

### build_assets (bash)
Building assets to generate TypeScript routes
```bash
npm run build
```

### ecl_page_template (vue)
Basic template for ECL demo pages with proper layout and structure
```vue
<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import EclLayout from '@/layouts/EclLayout.vue';

defineOptions({
    layout: EclLayout,
});
</script>

<template>
    <Head title="ECL Component Demo - European Commission" />

    <!-- Page Header -->
    <div class="ecl-page-header">
        <div class="ecl-container">
            <nav class="ecl-page-header__breadcrumb ecl-breadcrumb" aria-label="You are here:" data-ecl-breadcrumb>
                <ol class="ecl-breadcrumb__container">
                    <li class="ecl-breadcrumb__segment" data-ecl-breadcrumb-item>
                        <a href="/" class="ecl-link ecl-link--standalone ecl-link--no-visited ecl-breadcrumb__link">Home</a>
                        <svg class="ecl-icon ecl-icon--xs ecl-icon--rotate-90 ecl-breadcrumb__icon" 
                             focusable="false" 
                             aria-hidden="true">
                            <use href="/ecl-assets/icons/icons.svg#corner-arrow"></use>
                        </svg>
                    </li>
                    <li class="ecl-breadcrumb__segment ecl-breadcrumb__current-page" 
                        data-ecl-breadcrumb-item 
                        aria-current="page">
                        Component Demo
                    </li>
                </ol>
            </nav>
            <div class="ecl-page-header__body">
                <h1 class="ecl-page-header__title">ECL Component Demo</h1>
                <p class="ecl-page-header__description">
                    Demonstration of ECL components with examples and best practices.
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="ecl-u-pv-xl">
        <div class="ecl-container">
            <!-- Component examples go here -->
        </div>
    </main>
</template>
```

### ecl_table_example (html)
Example of a responsive ECL table with proper accessibility
```html
<div class="ecl-table-responsive">
    <table class="ecl-table" id="table-example">
        <caption class="ecl-table__caption">Table caption</caption>
        <thead class="ecl-table__head">
            <tr class="ecl-table__row">
                <th id="header-1" scope="col" class="ecl-table__header">Column 1</th>
                <th id="header-2" scope="col" class="ecl-table__header">Column 2</th>
            </tr>
        </thead>
        <tbody class="ecl-table__body">
            <tr class="ecl-table__row">
                <td data-ecl-table-header="Column 1" class="ecl-table__cell">Data 1</td>
                <td data-ecl-table-header="Column 2" class="ecl-table__cell">Data 2</td>
            </tr>
        </tbody>
    </table>
</div>
```

### navigation_links (vue)
Adding navigation links between related demo pages
```vue
<div class="ecl-u-bg-blue-5 ecl-u-pa-m" style="border-left: 4px solid #004494;">
    <p class="ecl-u-type-paragraph ecl-u-mb-s">
        <strong>Explore More Components:</strong>
    </p>
    <p class="ecl-u-type-paragraph">
        See comprehensive table examples including default, zebra-striped, multi-header, and sortable tables in the 
        <a href="/ecl-table-demo" class="ecl-link ecl-link--standalone">ECL Table Demo</a>.
    </p>
</div>
```

## Usage
All 5 snippet(s) are included above with full code. You can reference them by their 'ref' field.

## Next Actions
- Add another snippet: Use 'recipe_add_snippet' tool with recipe_id=5, ref=<unique_ref>, snippet=<code>, language=<language>, description=<description>
- Add an addendum: Use 'update_recipe' tool with id=5, addendum=<additional_notes_or_updates>