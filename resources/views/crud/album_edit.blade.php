<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Album') }}
        </h2>
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

                <form method="POST" action="{{ route('albumcrud.update', ['albumcrud' => $album->id ?? 0]) }}">
                    @csrf
                    @method('PUT')

                    {{-- Album Name --}}
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Album Name
                        </label>
                        <input type="text"
                               name="name"
                               id="name"
                               value="{{ old('name', $album->name) }}"
                               required
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm">

                        @error('name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Cover URL --}}
                    <div class="mb-4">
                        <label for="cover" class="block text-sm font-medium text-gray-700">
                            Cover URL
                        </label>
                        <input type="text"
                               name="cover"
                               id="cover"
                               value="{{ old('cover', $album->cover) }}"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm">

                        @error('cover')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Year --}}
                    <div class="mb-4">
                        <label for="year" class="block text-sm font-medium text-gray-700">
                            Release Year
                        </label>
                        <input type="number"
                               name="year"
                               id="year"
                               value="{{ old('year', $album->year) }}"
                               required
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm">

                        @error('year')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Genre --}}
                    <div class="mb-4">
                        <label for="genre" class="block text-sm font-medium text-gray-700">
                            Genre
                        </label>
                        <input type="text"
                               name="genre"
                               id="genre"
                               value="{{ old('genre', $album->genre) }}"
                               required
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm">

                        @error('genre')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Artist --}}
                    <div class="mb-4">
                        <label for="artist_id" class="block text-sm font-medium text-gray-700">
                            Artist
                        </label>
                        <select name="artist_id"
                                id="artist_id"
                                class="mt-1 block w-full rounded border-gray-300 shadow-sm">
                            @foreach ($artists as $artist)
                                <option value="{{ $artist->id }}"
                                    {{ old('artist_id', $album->artist_id) == $artist->id ? 'selected' : '' }}>
                                    {{ $artist->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('artist_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="flex justify-between">
                        <a href="{{ route('albumcrud.index') }}"
                           class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition">
                            Back
                        </a>

                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                            Confirm
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>
