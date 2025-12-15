<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Song') }}
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

                <form method="POST" action="{{ route('songcrud.update', ['songcrud' => $song->id ?? 0]) }}">
                    @csrf
                    @method('PUT')

                    {{-- Song Name --}}
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Song Name
                        </label>
                        <input type="text"
                               name="name"
                               id="name"
                               value="{{ old('name', $song->name) }}"
                               required
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm">

                        @error('name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Lyrics --}}
                    <div class="mb-4">
                        <label for="lyrics" class="block text-sm font-medium text-gray-700">
                            Song Lyrics
                        </label>
                        <input type="text"
                               name="lyrics"
                               id="lyrics"
                               value="{{ old('lyrics', $song->lyrics) }}"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm">

                        @error('lyrics')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Songwriter --}}
                    <div class="mb-4">
                        <label for="songwriter" class="block text-sm font-medium text-gray-700">
                            Songwriter
                        </label>
                        <input type="text"
                               name="songwriter"
                               id="songwriter"
                               value="{{ old('songwriter', $song->songwriter) }}"
                               required
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm">

                        @error('songwriter')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Album --}}
                    <div class="mb-6">
                        <label for="album_id" class="block text-sm font-medium text-gray-700">
                            Album
                        </label>
                        <select name="album_id"
                                id="album_id"
                                class="mt-1 block w-full rounded border-gray-300 shadow-sm">
                            @foreach($albums as $album)
                                <option value="{{ $album->id }}"
                                    {{ old('album_id', $song->album_id) == $album->id ? 'selected' : '' }}>
                                    {{ $album->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('album_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="flex justify-between">
                        <a href="{{ route('songcrud.index') }}"
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
