<x-app-layout>
    <x-slot name="header">
        <link rel="stylesheet" href="{{ asset('css/search.css') }}">
        
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Search Results for: ') }} "{{ $query }}"
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">

                @if($error)
                    <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
                        {{ $error }}
                    </div>
                @endif

                @if($results->isNotEmpty())
                    <div class="search-grid">
                        @foreach($results as $result)
                            @php
                                $id = $result->searchable['id'];
                                $artistId = $result->searchable['artist_id'] ?? null;

                                if ($result->type === 'albums') {
                                    $url = url("artist/{$artistId}/{$id}");
                                } else {
                                    $url = url("artist/{$id}");
                                }
                                
                                $image = $result->searchable['cover'] ?? $result->searchable['image'] ?? asset('images/default.jpg');
                            @endphp

                            <a href="{{ $url }}" class="search-btn">
                                <div class="flex flex-col items-center">
                                    <img class="search-img" src="{{ $image }}" alt="{{ $result->title }}">
                                    <span class="search-title">{{ $result->title }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-10">No results found for "{{ $query }}".</p>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>