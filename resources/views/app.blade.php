<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Campus Digitale Forma') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="/assets/favicon.svg" />
    <meta name="theme-color" content="#0B3B5E">

    <!-- Open Graph -->
    <meta property="og:title" content="Campus Digitale Forma" />
    <meta property="og:description" content="Trasforma l'apprendimento in valore." />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="/assets/cdf-og-1200x630.svg" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <meta name="twitter:card" content="summary_large_image" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Vimeo Player API -->
    <script src="https://player.vimeo.com/api/player.js"></script>
</head>
<body class="font-sans antialiased">
    <div id="app"></div>
</body>
</html>
