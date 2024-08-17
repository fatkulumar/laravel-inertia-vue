<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'Madrasah MPJ') }}</title>
    <meta name="title" content="Madrasah MPJ" />
    <meta name="description"
        content="Komunitas Yang Memiliki Visi Terwujudnya Sinergi Antar Media Dakwah Pondok Pesantren Ala Ahlusunnah Wal Jama'ah An Nahdiyah Dan Sebagai Pusat Informasi Pondok Pesantren se-Jawa Timur.">
    <meta name="keywords" content="madrasah media pondok jawa timur, madrasah, madrasah mmpj, mmpj">
    <meta property="og:title" content="Madrasah MPJ">
    <meta property="og:description" content="Media Dakwah Pondok Pesantren Ala Ahlusunnah Wal Jama'ah An Nahdiyah.">
    <meta property="og:image" content="{{ asset('favicon.ico') }}">
    <meta property="og:url" content="{{ env('APP_URL') }}">


    <meta name="robots" content="index, follow" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="id" />
    <meta name="author" content="Media Pondok Jawa Timur" />

    <!-- Social media share -->
    <meta property="og:title" content="Media Pondok Jawa Timur" />
    <meta property="og:site_name" content="mediapondokjatim" />
    <meta property="og:url" content="https://mediapondokjatim.com" />
    <meta property="og:description" content="Media Dakwah Pondok Pesantren Ala Ahlusunnah Wal Jama'ah An Nahdiyah" />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="{{ asset('favicon.ico') }}" />
    {{-- <meta name="twitter:card" content="summary" />
    <meta name="twitter:site" content="@altinindo" />
    <meta name="twitter:creator" content="@altinindo" />
    <meta name="twitter:title" content="Media Pondok Jawa Timur" />
    <meta name="twitter:description" content="Media Dakwah Pondok Pesantren Ala Ahlusunnah Wal Jama'ah An Nahdiyah" />
    <meta name="twitter:image" content="{{ asset('favicon.ico') }}" /> --}}

    <meta property="og:title" content="Media Pondok Jawa Timur" />
    <meta property="og:description" content="Media Dakwah Pondok Pesantren Ala Ahlusunnah Wal Jama'ah An Nahdiyah" />
    <meta property="og:url" content="https://www.facebook.com/pondokjatim" />
    <meta property="og:image" content="{{ asset('favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('/build/assets/app-B_uctIi3.css') }}">

    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body class="font-sans antialiased h-full">
    @inertia

    <script src="{{ asset('/build/assets/app-DXFqecGY.js') }}"></script>
</body>

</html>
