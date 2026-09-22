<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title inertia>{{ config('app.name', 'SIAKAD') }}</title>

        {{-- Cegah kedip tema terang saat memuat halaman dalam mode gelap. --}}
        <script>
            (function () {
                try {
                    var saved = localStorage.getItem('siakad-theme');
                    var dark = saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches);
                    document.documentElement.classList.toggle('dark', dark);
                } catch (e) {}
            })();
        </script>

        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @inertiaHead
        @routes
    </head>
    <body class="antialiased bg-surface-muted text-content">
        @inertia
    </body>
</html>
