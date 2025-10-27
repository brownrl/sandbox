# Sandbox - Laravel 12 + Vue 3 + Inertia Development Guide

## Build & Development Commands
- **Build frontend**: `npm run build` (ALWAYS prefer this - single-step builds are preferred)
- **Dev mode**: `composer run dev` (runs server, queue, logs, vite concurrently)
- **NEVER suggest**: `npm run dev` alone - prefer single builds over background watch processes
- **Format code**: `npm run format` (Prettier) or `npm run lint` (ESLint)
- **PHP formatting**: `vendor/bin/pint --dirty` (run before finalizing changes)

## Testing Commands
- **All tests**: `php artisan test`
- **Single test file**: `php artisan test tests/Feature/ExampleTest.php`
- **Single test method**: `php artisan test --filter=testName`
- Always run affected tests after changes, write tests for all new features

## Code Style - PHP
- Use PHP 8+ constructor property promotion: `public function __construct(public Foo $foo) {}`
- Always use explicit return types: `public function show(): Response`
- Models use `casts()` method (not `$casts` property) - Laravel 12 style
- Run `vendor/bin/pint --dirty` before committing
- Use Eloquent over raw queries, eager load to prevent N+1 queries

## Code Style - Vue/TypeScript
- Vue 3 Composition API with `<script setup lang="ts">`
- Import routes: `import { routeName } from '@/routes'` (auto-generated via Wayfinder)
- Use `@/` alias for `resources/js/` imports
- Components in `resources/js/pages/` (pages) or `resources/js/components/` (reusable)
- Single root element per Vue component

## Code Style - CSS/Tailwind
- Tailwind CSS 4.1 - use `@import "tailwindcss"` (not `@tailwind` directives)
- Prefer Tailwind utilities over custom CSS
- Use `gap` utilities for spacing (not margins)
- Consistent gradient backgrounds: `bg-gradient-to-br from-slate-900 to-purple-900`

## Architecture Patterns
- Backend: Controllers return `Inertia::render('ComponentName', $data)`
- Routes: Add in `routes/web.php`, then `npm run build` to regenerate TypeScript routes
- Validation: Use Form Request classes (not inline validation)
- Auth: Laravel Fortify with 2FA support
- Database: SQLite with JSON columns for array storage (questions/responses)

## Important Notes
- Local dev: `https://sandbox.test` (Laravel Herd manages HTTPS automatically)
- After route changes: Run `npm run build` to regenerate TypeScript route definitions
- Never edit files in `resources/js/routes/` - they're auto-generated
- Use factories/seeders when creating models
- Follow existing conventions - check sibling files before creating new patterns
