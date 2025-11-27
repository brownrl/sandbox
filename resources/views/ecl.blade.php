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

    <link rel="stylesheet" href="{{ asset('ecl-assets/css/ecl-reset.css') }}">
    <link rel="stylesheet" href="{{ asset('ecl-assets/css/ecl-ec.css') }}">
    <link rel="stylesheet" href="{{ asset('ecl-assets/css/ecl-ec-utilities.css') }}">
    <link rel="stylesheet" href="{{ asset('ecl-assets/css/ecl-ec-print.css') }}" media="print">

    @vite(['resources/js/ecl.ts', "resources/js/pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body>
    @inertia

    <script src="{{ asset('ecl-assets/js/ecl-ec.js') }}"></script>
    <script>
        // Initial load
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof ECL !== 'undefined' && ECL.autoInit) {
                ECL.autoInit();
            }
        });

        // Reinitialize on Inertia navigation
        document.addEventListener('inertia:navigate', function() {
            if (typeof ECL !== 'undefined' && ECL.autoInit) {
                // Small delay to ensure DOM is updated
                setTimeout(function() {
                    ECL.autoInit();
                }, 50);
            }
        });
    </script>
</body>

</html>
