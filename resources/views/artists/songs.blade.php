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
                    <!-- Playlist -->
                    <div class="playlist-container">
                        <h3 class="playlist-title">{{ count($songs) }} Tracks</h3>
                        @foreach($songs as $index => $song)
                            <div class="song-item" data-index="{{ $index }}" data-url="{{ $song->stream_url }}">
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
        // Initialize the global music player with songs from this page
        const songsData = <?php echo json_encode($songs ?? []); ?>;
        window.musicPlayer.init(songsData);

        // Add click handlers for play buttons
        document.addEventListener('DOMContentLoaded', function() {
            const playButtons = document.querySelectorAll('.song-play-btn');
            playButtons.forEach((btn, index) => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    window.musicPlayer.loadSong(index);
                    document.getElementById('audioPlayer').play();
                    document.getElementById('playBtn').innerHTML = '<i class="fas fa-pause"></i>';
                    document.getElementById('coverPlayBtn').innerHTML = '<i class="fas fa-pause"></i>';
                });
            });
        });
    </script>
</x-app-layout>
