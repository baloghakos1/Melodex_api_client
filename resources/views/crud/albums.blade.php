<x-app-layout>
    <x-slot name="header">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="{{ asset('css/crudindex.css') }}">

        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Albums') }}
        </h2>

        {{-- Select Data Table --}}
        <form method="GET" action="{{ request()->url() }}" class="select_crud">
            <label for="crud" class="font-semibold">
                {{ __('Select Data table: ') }}
            </label>

            <select name="crud" id="crud" 
                    class="border-gray-300 rounded-lg shadow-sm"
                    onchange="location = this.value">

                <option value="{{ route('crud.index') }}">-- Data tables --</option>
                <option value="{{ route('crud.artists') }}">Artists</option>
                <option value="{{ route('crud.members') }}">Members</option>
                <option value="{{ route('crud.albums') }}" selected>Albums</option>
                <option value="{{ route('crud.songs') }}">Songs</option>
            </select>
        </form>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">

                {{-- Error Alert --}}
                @if($error)
                    <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
                        {{ $error }}
                    </div>
                @endif

                {{-- Albums Table Header --}}
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">Albums Table</h2>

                    <a href="{{ route('albumcrud.create') }}" 
                       class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                        <button>
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </a>
                </div>

                <br>

                {{-- Albums Table --}}
                <div>
                    @if($albums->isEmpty())
                        <p class="text-gray-500 italic">No albums found.</p>
                    @else
                        {{-- Responsive Wrapper --}}
                        <div class="overflow-x-auto">
                            <table class="w-full table-auto border border-gray-300 rounded-lg">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 border">ID</th>
                                        <th class="px-4 py-2 border">Name</th>
                                        <th class="px-4 py-2 border">Cover</th>
                                        <th class="px-4 py-2 border">Year</th>
                                        <th class="px-4 py-2 border">Genre</th>
                                        <th class="px-4 py-2 border">Artist</th>
                                        <th class="px-4 py-2 border w-32">Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($albums as $album)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-4 py-2 border">{{ $album->id }}</td>
                                            <td class="px-4 py-2 border">{{ $album->name }}</td>
                                            <td class="px-4 py-2 border">
                                                <img src="{{ $album->cover }}" 
                                                    alt="{{ $album->name }}" 
                                                    class="w-16 h-16 object-cover rounded shadow">
                                            </td>
                                            <td class="px-4 py-2 border">{{ $album->year }}</td>
                                            <td class="px-4 py-2 border">{{ $album->genre }}</td>
                                            <td class="px-4 py-2 border">{{ $album->artist->name ?? 'N/A' }}</td>
                                            <td class="px-4 py-2 border text-center">
                                                {{-- Edit --}}
                                                <a href="{{ route('albumcrud.edit', $album->id) }}" 
                                                class="text-blue-600 hover:text-blue-800 mx-1">
                                                    <i class="fa-solid fa-pencil"></i>
                                                </a>

                                                {{-- Delete --}}
                                                <form action="{{ route('albumcrud.destroy', $album->id) }}" 
                                                    method="POST" 
                                                    class="inline-block mx-1" 
                                                    onsubmit="return confirm('Are you sure?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>


            </div>
        </div>
    </div>

</x-app-layout>
