<x-app-layout>
    <x-slot name="header">
        <link rel="stylesheet" href="{{ asset('css/artists.css') }}">
        <link rel="stylesheet" href="{{ asset('css/songs.css') }}">
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

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">

                @if($error)
                    <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
                        {{ $error }}
                    </div>
                @else
                    @foreach($songs as $song)
                        <div class="song-info">
                            <img class="song-cover" src="{{ $album->cover }}" alt="{{ $album->name }}">
                            <div class="song-details">
                                <h1 class="song-name">{{ $song->name }}</h1>
                                <h3 class="song-artist">{{ $artist->name ?? 'Unknown Artist' }}</h3>
                            </div>
                        </div>
                    @endforeach
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
