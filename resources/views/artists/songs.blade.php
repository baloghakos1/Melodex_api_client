<x-app-layout>
    <x-slot name="header">
        <link rel="stylesheet" href="{{ asset('css/artists.css') }}">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $album->name ?? 'Album' }}
        </h2>
        @if($artist)
        <a href="{{ route('artists.show', ['artist_id' => $artist->id]) }}"
           class="text-blue-600 hover:text-blue-800">
            &larr; Back to Artist
        </a>
        @endif
    </x-slot>

    <div class="py-6 pb-96">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">

                @if($error)
                    <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
                        {{ $error }}
                    </div>
                @else
                    <div class="playlist-container">
                        <h3 class="playlist-title">{{ count($songs) }} Tracks</h3>
                        @foreach($songs as $index => $song)
                            <div class="song-item" data-index="{{ $index }}">
                                <span class="song-index">{{ $index + 1 }}</span>
                                <div class="song-cover-wrapper">
                                    <img class="song-cover-small" src="{{ $album->cover }}" alt="{{ $album->name }}">
                                    <button class="song-play-btn" title="Play">
                                        <i class="fas fa-play"></i>
                                    </button>
                                </div>
                                <div class="song-info-small">
                                    <h4 class="song-name-small">{{ $song->name }}</h4>
                                    <p class="song-artist-small">{{ $artist->name ?? 'Unknown Artist' }}</p>
                                </div>
                                <span class="song-duration">--:--</span>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </div>
    </div>

    <script>
        function initSongsPage() {
            window._musicPlayerInitialized = true;

            const songsData = @json($songs).map(song => ({
                ...song,
                artist_name: "{{ $artist->name ?? 'Unknown Artist' }}",
                album: { cover: "{{ $album->cover ?? '' }}" }
            }));

            window.musicPlayer.init(songsData);

            document.querySelectorAll('.song-play-btn').forEach((btn, index) => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    window.musicPlayer.loadSong(index, true);
                });
            });
        }

        document.addEventListener('DOMContentLoaded', initSongsPage);
        document.addEventListener('turbo:load', initSongsPage);
    </script>
</x-app-layout>