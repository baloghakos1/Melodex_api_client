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
                    <div 
                        x-data="{ open: false }" 
                        class="playlist-info mb-4 p-4 bg-gray-100 rounded-lg flex justify-between items-center relative z-0"
                    >
                        <!-- Playlist Name -->
                        <h3 class="text-xl font-semibold">
                            {{ $playlist->name }}
                        </h3>

                        <!-- 3 Dots Button -->
                        <div class="relative">
                            <button 
                                @click="open = !open"
                                class="text-gray-600 hover:text-gray-900 focus:outline-none"
                            >
                                <i class="fa-solid fa-ellipsis-vertical text-xl"></i>
                            </button>

                            <!-- Dropdown Menu -->
                            <div 
                                x-show="open" 
                                @click.away="open = false"
                                x-transition
                                class="absolute right-0 top-8 w-40 bg-white border rounded-lg shadow-xl z-50"
                            >
                                <a href="{{ route('playlists.edit', $playlist->id) }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Edit
                                </a>

                                <form action="{{ route('playlists.destroy', $playlist->id) }}" 
                                    method="POST"
                                    onsubmit="return confirm('Delete this playlist?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach

                @endif

            </div>
        </div>
    </div>
</x-app-layout>
