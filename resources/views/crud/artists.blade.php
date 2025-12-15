<x-app-layout>
    <x-slot name="header">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="{{ asset('css/crudindex.css') }}">

        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Artists') }}
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
                <option value="{{ route('crud.artists') }}" selected>Artists</option>
                <option value="{{ route('crud.members') }}">Members</option>
                <option value="{{ route('crud.albums') }}">Albums</option>
                <option value="{{ route('crud.songs') }}">Songs</option>
            </select>
        </form>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">

                {{-- Flash Success Message --}}
                @if (session('success'))
                    <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Flash Error Message --}}
                @if (session('error'))
                    <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Artists Table Header --}}
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">Artists Table</h2>

                    <a href="{{ route('artistcrud.create') }}"
                       class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                        <i class="fa-solid fa-plus"></i>
                    </a>
                </div>

                <br>

                {{-- Artists Table --}}
                <div>
                    @if($artists->isEmpty())
                        <p class="text-gray-500 italic">No artists found.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full table-auto border border-gray-300 rounded-lg">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 border">ID</th>
                                        <th class="px-4 py-2 border">Name</th>
                                        <th class="px-4 py-2 border">Nationality</th>
                                        <th class="px-4 py-2 border">Image</th>
                                        <th class="px-4 py-2 border">Description</th>
                                        <th class="px-4 py-2 border">Is Band</th>
                                        <th class="px-4 py-2 border w-32">Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($artists as $artist)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-4 py-2 border">{{ $artist->id }}</td>
                                            <td class="px-4 py-2 border">{{ $artist->name }}</td>
                                            <td class="px-4 py-2 border">{{ $artist->nationality }}</td>

                                            <td class="px-4 py-2 border">
                                                <img src="{{ $artist->image }}"
                                                     alt="{{ $artist->name }}"
                                                     class="w-16 h-16 object-cover rounded shadow">
                                            </td>

                                            <td class="px-4 py-2 border text-sm">
                                                {{ Str::limit($artist->description, 80) }}
                                            </td>

                                            <td class="px-4 py-2 border text-center">
                                                {{ $artist->is_band ? 'Yes' : 'No' }}
                                            </td>

                                            <td class="px-4 py-2 border text-center">
                                                {{-- Edit --}}
                                                <a href="{{ route('artistcrud.edit', $artist->id) }}"
                                                   class="text-blue-600 hover:text-blue-800 mx-1">
                                                    <i class="fa-solid fa-pencil"></i>
                                                </a>

                                                {{-- Delete --}}
                                                <form action="{{ route('artistcrud.destroy', $artist->id) }}"
                                                      method="POST"
                                                      class="inline-block mx-1"
                                                      onsubmit="return confirm('Are you sure?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="text-red-600 hover:text-red-800">
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
