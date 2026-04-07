<x-app-layout>
    <x-slot name="header">
        <link rel="stylesheet" href="{{ asset('css/artists.css') }}">
        <link rel="stylesheet" href="{{ asset('css/songs.css') }}">
        <link rel="stylesheet" href="{{ asset('css/playlist_songs.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $playlist->name ?? 'Playlist' }}
        </h2>
        <a href="{{ route('playlists.index') }}" class="text-blue-600 hover:text-blue-800">
            &larr; Back to Playlists
        </a>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
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
                @endif

                @if($songs->isEmpty())
                    <div class="text-gray-700 p-4">
                        No songs in this playlist.
                    </div>
                @else
                @foreach($songs as $song)
                    <div x-data="{ open: false }" class="song-row-wrapper">

                        <div class="song-row">

                            <!-- Song Info -->
                            <div class="song-left">
                                <img class="song-cover"
                                    src="{{ $song->album_cover ?? asset('image/default_song.png') }}"
                                    alt="{{ $song->album_name }}">

                                <div class="song-details">
                                    <h1 class="song-name">{{ $song->name }}</h1>
                                    <h3 class="song-artist">{{ $song->artist_name ?? 'Unknown Artist' }}</h3>
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
                                        <a @click.prevent="showModal = true">Add to other playlist</a>

                                        <!-- Modal -->
                                        <div x-show="showModal" class="modal-backdrop fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                                            <div class="bg-white rounded-lg p-6 w-96 relative">
                                                <h3 class="text-lg font-bold mb-4">Select playlists</h3>
                                                <form method="POST" action="{{ route('playlist.storeAddToPlaylists', $song->id) }}">
                                                    @csrf
                                                    @foreach($userPlaylists as $playlist)
                                                        <div class="mb-2">
                                                            <label class="flex items-center space-x-2">
                                                                <input type="checkbox" name="playlists[]" value="{{ $playlist->id }}" class="h-4 w-4">
                                                                <span>{{ $playlist->name }}</span>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                    <div class="flex justify-end space-x-2 mt-4">
                                                        <button type="button" @click="showModal = false" class="px-4 py-2 border rounded">Cancel</button>
                                                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Add</button>
                                                    </div>
                                                </form>
                                                <button @click="showModal = false" class="absolute top-2 right-2 text-gray-500 hover:text-gray-800">&times;</button>
                                            </div>
                                        </div>
                                    </div>
                                    <form action="{{ route('playlist.removeSong', [$playlist->id, $song->id]) }}"
                                        method="POST"
                                        onsubmit="return confirm('Remove this song from the playlist?');">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="song-remove-btn">
                                            Remove from Playlist
                                        </button>

                                    </form>

                                </div>

                            </div>

                        </div>

                    </div>
                    @endforeach
                @endif

            </div>
        </div>
    </div>
</x-app-layout>