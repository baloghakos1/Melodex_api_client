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
               class="text-blue-600 hover:text-blue-800"
               data-turbo="false">
                &larr; Back to Artist
            </a>
        @endif
    </x-slot>
    <div class="py-6 pb-96">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- ✅ FIXED WRAPPER -->
            <div class="bg-white shadow sm:rounded-lg p-6 overflow-visible">

                {{-- Flash Messages --}}
                @if (session('success'))
                    <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                @if($songs->isEmpty())
                    <div class="text-gray-700 p-4">
                        No songs found in this album.
                    </div>
                @else

                <div class="playlist-container overflow-visible">
                    <h3 class="playlist-title">{{ count($songs) }} Tracks</h3>

                    @foreach($songs as $index => $song)
                        <div x-data="{ open: false }"
                            :class="{ 'z-[1000]': open }"
                            class="song-item flex items-center justify-between relative">

                            <!-- LEFT -->
                            <div class="flex items-center space-x-4">
                                <span class="song-index">{{ $index + 1 }}</span>

                                <div class="song-cover-wrapper">
                                    <img class="song-cover-small"
                                         src="{{ $album->cover }}"
                                         alt="{{ $album->name }}">

                                    <button class="song-play-btn" title="Play">
                                        <i class="fas fa-play"></i>
                                    </button>
                                </div>

                                <div class="song-info-small">
                                    <h4 class="song-name-small">{{ $song->name }}</h4>
                                    <p class="song-artist-small">
                                        {{ $artist->name ?? 'Unknown Artist' }}
                                    </p>
                                </div>
                            </div>

                            <!-- RIGHT -->
                            <div class="flex items-center space-x-4">
                                <span class="song-duration">--:--</span>

                                <!-- DROPDOWN -->
                                <div x-data="{ open: false }"
                                    :class="{ 'z-50': open }"
                                    class="relative"
                                >
                                    <button @click="open = !open" class="song-dots-btn">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>

                                    <div x-show="open"
                                         @click.away="open = false"
                                         x-transition
                                         class="absolute right-0 mt-2 w-48 bg-white text-black shadow-lg rounded z-[999]"
                                         style="display:none">

                                        <a @click.prevent="$dispatch('open-modal', {{ $song->id }}); open = false"
                                            class="block px-4 py-2 cursor-pointer hover:bg-gray-100">

                                            Add to playlist
                                        </a>

                                    </div>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>

                @endif

            </div>
        </div>
    </div>

    <div
        x-data="{ open: false, songId: null }"
        x-on:open-modal.window="open = true; songId = $event.detail"
        x-show="open"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999]"
        style="display:none;"
    >
        <div class="bg-white text-black rounded-lg p-6 w-96 relative">

            <h3 class="text-lg font-bold mb-4 text-gray-900">
                Select playlists
            </h3>

            <!-- ✅ FIXED: dynamic action -->
            <form method="POST" action="{{ route('playlist.syncSongPlaylists', $song->id) }}">
                @csrf

                @foreach($userPlaylists as $playlistItem)
                    <div class="mb-2">
                        <label class="flex items-center space-x-2 text-gray-800">
                            <input type="checkbox"
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
                    <button type="button"
                            @click="open = false"
                            class="px-4 py-2 border rounded text-gray-800">
                        Cancel
                    </button>

                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded">
                        Ok
                    </button>
                </div>
            </form>

            <button @click="open = false"
                    class="absolute top-2 right-2 text-gray-500 hover:text-gray-800">
                &times;
            </button>

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