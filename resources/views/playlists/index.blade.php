<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Playlists') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">

                @if ($error)
                    <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
                        {{ $error }}
                    </div>
                @endif

                @if ($playlists->isEmpty())
                    <div class="text-gray-700">
                        No playlists found.
                    </div>
                @else
                    @foreach ($playlists as $playlist)
                        <div class="playlist-info mb-4 p-4 bg-gray-100 rounded-lg">
                            <h3 class="text-xl font-semibold">{{ $playlist->name }}</h3>
                        </div>
                    @endforeach
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
