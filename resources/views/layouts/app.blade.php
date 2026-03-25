<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Melodex') }}</title>

        <link rel="icon" type="image/x-icon" href="{{ asset('front_view.ico') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="{{ asset('css/songs.css') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts (Turbo is imported inside app.js) -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- Global Music Player — data-turbo-permanent keeps it alive across page navigations -->
        <div id="music-player-permanent" data-turbo-permanent>
            <x-music-player />
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (!window._musicPlayerInitialized) {
                    window.musicPlayer.init();
                }
            });

            window._wasPlaying = null;

            document.addEventListener('turbo:click', function () {
                const audio = document.getElementById('audioPlayer');
                window._wasPlaying = audio && !audio.paused;
            });

            document.addEventListener('turbo:load', function () {
                const audio = document.getElementById('audioPlayer');

                if (window._wasPlaying === true && audio && audio.src && audio.paused) {
                    audio.play().catch(() => {});
                }

                window._wasPlaying = null;

                if (window.musicPlayer && window.musicPlayer.songs.length > 0) {
                    window.musicPlayer.bindSongItems();
                }

                window._musicPlayerInitialized = false;
            });
        </script>
        @stack('scripts')
    </body>
</html>