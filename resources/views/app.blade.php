<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title inertia>{{ config('app.name', 'SIAKAD') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @inertiaHead
        @routes
    </head>
    <body class="antialiased bg-slate-50 text-slate-900">
        @inertia
    </body>
</html>
