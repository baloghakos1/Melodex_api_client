<x-app-layout>
    <x-slot name="header">
        <link rel="stylesheet" href="{{ asset('css/artists.css') }}">
        <link rel="stylesheet" href="{{ asset('css/songs.css') }}">
        <link rel="stylesheet" href="{{ asset('css/playlist_songs.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $album->name ?? 'Album' }}
        </h2>
        @if($artist)
        <a href="{{ route('artists.show', ['artist_id' => $artist->id]) }}"
           class="text-blue-600 hover:text-blue-800" data-turbo="false">
            &larr; Back to Artist
        </a>
        @endif
    </x-slot>

    <div class="py-6 pb-96">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">

                {{-- Flash Success Message --}}
                @if (session('success'))
                    <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Flash Error Message --}}
                @if (session('error'))
                    <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
                        {{ session('error') }}
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
                @foreach($songs as $song)
                <div x-data="{ open: false }" class="song-row-wrapper">
                
                    <div class="song-row">
                
                        <!-- Song Info -->
                        <div class="song-left">
                            <img class="song-cover" src="{{ $album->cover }}" alt="{{ $album->name }}">
                
                            <div class="song-details">
                                <h1 class="song-name">{{ $song->name }}</h1>
                                <h3 class="song-artist">{{ $artist->name ?? 'Unknown Artist' }}</h3>
                            </div>
                        </div>
                
                        <!-- 3 Dots Menu -->
                        <div class="song-menu">
                
                            <!-- Button -->
                            <button @click="open = !open" class="song-dots-btn">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                
                            <!-- Dropdown -->
                            <div
                                x-show="open"
                                @click.away="open = false"
                                x-transition
                                class="song-dropdown"
                                style="display:none"
                            >
                                <div x-data="{ showModal: false }">
                                    <a @click.prevent="showModal = true">Add to playlist</a>
                
                                    <!-- Modal -->
                                    <div x-show="showModal" class="modal-backdrop fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                                        <div class="bg-white rounded-lg p-6 w-96 relative">
                                            <h3 class="text-lg font-bold mb-4">Select playlists</h3>
                
                                            <form method="POST" action="{{ route('playlist.syncSongPlaylists', $song->id) }}">
                                                @csrf
                
                                                @foreach($userPlaylists as $playlistItem)
                                                    <div class="mb-2">
                                                        <label class="flex items-center space-x-2">
                                                            <input 
                                                                type="checkbox" 
                                                                name="playlists[]" 
                                                                value="{{ $playlistItem->id }}"
                                                                class="h-4 w-4"
                                                                {{ in_array($playlistItem->id, $song->playlist_ids ?? []) ? 'checked' : '' }}
                                                            >
                                                            <span>{{ $playlistItem->name }}</span>
                                                        </label>
                                                    </div>
                                                @endforeach
                
                                                <div class="flex justify-end space-x-2 mt-4">
                                                    <button type="button" @click="showModal = false" class="px-4 py-2 border rounded">Cancel</button>
                                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Ok</button>
                                                </div>
                                            </form>
                
                                            <button @click="showModal = false" class="absolute top-2 right-2 text-gray-500 hover:text-gray-800">&times;</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                
                        </div>
                
                    </div>
                </div>
                @endforeach
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