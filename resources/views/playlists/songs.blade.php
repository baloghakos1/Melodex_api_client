<x-app-layout>
    <x-slot name="header">
        <link rel="stylesheet" href="{{ asset('css/artists.css') }}">
        <link rel="stylesheet" href="{{ asset('css/songs.css') }}">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $playlist->name ?? 'Playlist' }}
        </h2>
        <a href="{{ route('playlists.index') }}" class="text-blue-600 hover:text-blue-800">
            &larr; Back to Playlists
        </a>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">

                @if($error)
                    <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
                        {{ $error }}
                    </div>
                @elseif($songs->isEmpty())
                    <div class="text-gray-700 p-4">
                        No songs in this playlist.
                    </div>
                @else
                    @foreach($songs as $song)
                        <div class="song-info flex items-center gap-4 mb-4">
                            <img class="song-cover w-20 h-20 object-cover rounded" 
                                 src="{{ $song->album_cover ?? asset('image/default_song.png') }}" 
                                 alt="{{ $song->album_name }}">
                            <div class="song-details">
                                <h1 class="song-name">{{ $song->name }}</h1>
                                <h3 class="song-artist">{{ $song->artist_name ?? 'Unknown Artist' }}</h3>
                            </div>
                        </div>
                    @endforeach
                @endif

            </div>
        </div>
    </div>
</x-app-layout>