<x-app-layout>
    <x-slot name="header">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="{{ asset('css/crudindex.css') }}">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Playlists') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">

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
                
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">Playlists</h2>

                    <a href="{{ route('playlists.create') }}"
                    class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                        <i class="fa-solid fa-plus"></i>
                    </a>
                </div>

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
