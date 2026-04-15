<x-app-layout>
    <x-slot name="header">
        <link rel="stylesheet" href="{{ asset('css/description.css') }}">
        <link rel="stylesheet" href="{{ asset('css/artists.css') }}">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $artist->name ?? 'Artist Description' }}
        </h2>
        <a href="{{ route('artists.show', ['artist_id' => $artist->id]) }}"
           class="text-blue-600 hover:text-blue-800" data-turbo="false">
            &larr; Back to Artist
        </a>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">

                @if($error)
                    <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
                        {{ $error }}
                    </div>
                @endif

                <div class="artist-container">
                    <div class="members-column">
                        <p>Artist: {{ $artist->name }}</p>
                        <img class="artist-img rounded-lg" src="{{ $artist->image }}" alt="{{ $artist->name }}">
                    </div>

                    <div class="description-container">
                        <h1 class="description-name">Description</h1>
                        <h3 class="description">{{ $artist->description }}</h3>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>