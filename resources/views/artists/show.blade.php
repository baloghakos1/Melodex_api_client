<x-app-layout>
    <x-slot name="header">
        <link rel="stylesheet" href="{{ asset('css/albums.css') }}">
        <link rel="stylesheet" href="{{ asset('css/artists.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $artist->name }}
        </h2>
        <div class="mt-4">
            <a href="{{ route('artists.index') }}"
                class="text-blue-600 hover:text-blue-800">
                &larr; Back to Artists
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">

                @if($error)
                    <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
                        {{ $error }}
                    </div>
                @endif

                <div class="flex items-center gap-6 mb-8">
                    <img src="{{ $artist->image ?? asset('image/default_artist.png') }}"
                         alt="{{ $artist->name }}"
                         class="artist-photo">

                    <div>
                        <a href="{{ route('artists.description', ['artist_id' => ($artist->id)]) }}">
                            <button class="read-more-btn">
                                <i class="fa-solid fa-square-plus mr-1"></i> Read More
                            </button>
                        </a>
                    </div>
                </div>

                @if($albums->isEmpty())
                    <p class="text-gray-600">No albums available.</p>
                @else
                    <div class="album-container">

                        @foreach($albums as $album)
                            <a href="{{ route('artists.songs', [
                                'artist_id' => $artist->id,
                                'album_id' => $album->id
                            ]) }}"
                               class="block bg-gray-50 hover:bg-gray-100 p-4 rounded-lg shadow transition">
                               <div class="album-info">
                                    <button class="album-cover-btn">
                                    <img src="{{ $album->cover }}"
                                        alt="{{ $album->name }}"
                                        class="album-cover">

                                    <h1 class="album-name">{{ $album->name }}</h1>
                                    <h3 class="album-release">{{ $album->year }}</h3>
                                    <h4 class="album-songs-count">{{ $album->songs_count }} songs</h4>
                                    </button>
                                </div>
                            </a>
                        @endforeach

                    </div>
                @endif

            </div>

        </div>
    </div>
</x-app-layout>
