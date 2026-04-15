<x-app-layout>
    <x-slot name="header">
        <link rel="stylesheet" href="{{ asset('css/artists.css') }}">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Artists') }}
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

                @if($artists->isNotEmpty())
                    <div class="artist-grid">
                        @foreach($artists as $artist)
                            <a href="{{ url('artist/' . ($artist->id)) }}" data-turbo="false">
                                <button type="button" class="artist-btn flex flex-col items-center">
                                    <img class="artist-img rounded-lg" src="{{ $artist->image }}" alt="{{ $artist->name }}">
                                    <span class="artist-name mt-2 font-medium">{{ $artist->name }}</span>
                                </button>
                            </a>
                        @endforeach
                    </div>
                @else
                <p class="text-gray-500">No artists found.</p>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>