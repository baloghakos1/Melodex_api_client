<x-app-layout>
    <x-slot name="header">
        <link rel="stylesheet" href="{{ asset('css/description.css') }}">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $artist->name ?? 'Artist Description' }}
        </h2>
        <a href="{{ route('artists.show', ['artist_id' => $artist->id]) }}"
           class="text-blue-600 hover:text-blue-800">
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
                        @if($artist->is_band === 'yes')
                            <h3>Band Members:</h3>
                            @foreach($members as $member)
                                <div class="member-container">
                                    <img src="{{ $member->image }}" alt="{{ $member->name }}" class="member-photo">
                                    <div class="member-info">
                                        <h1 class="member-name">{{ $member->name }} ({{ $member->year }})</h1>
                                        <h3 class="member-instrument">{{ $member->instrument }}</h3>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p>Solo Artist: {{ $artist->name }}</p>
                        @endif
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