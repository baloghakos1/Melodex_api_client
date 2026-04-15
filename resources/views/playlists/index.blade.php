<x-app-layout>
    <x-slot name="header">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="{{ asset('css/playlist.css') }}">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Playlists') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">

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
                <div x-data="{ open: false }" class="relative mb-4">

                    <!-- Playlist Card -->
                    <div class="playlist-card-btn flex justify-between items-center">
                        <a href="{{ route('playlists.songs', $playlist->id) }}" class="flex items-center gap-4 flex-1" data-turbo="false">
                            <div class="flex items-center gap-4">
                                <div class="playlist-icon">
                                    <i class="fa-solid fa-music"></i>
                                </div>
                                <div class="playlist-text">
                                    <h1 class="playlist-name">{{ $playlist->name }}</h1>
                                    <p class="playlist-sub">View songs</p>
                                </div>
                            </div>
                        </a>

                        <!-- 3 Dots Button Inside Card -->
                        <div class="relative">
                            <button @click="open = !open" class="playlist-dots-btn">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" @click.away="open = false" x-transition class="playlist-dropdown" style="display: none;">
                                <a href="{{ route('playlists.edit', $playlist->id) }}" data-turbo="false">Edit</a>
                                <form action="{{ route('playlists.destroy', $playlist->id) }}" method="POST" onsubmit="return confirm('Delete this playlist?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-btn">Delete</button>
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
