<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Artist') }}
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

                <form method="POST" action="{{ route('artistcrud.update', ['artistcrud' => $artist->id ?? 0]) }}">
                    @csrf
                    @method('PUT')

                    {{-- Name --}}
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Artist Name
                        </label>
                        <input type="text"
                               name="name"
                               id="name"
                               value="{{ old('name', $artist->name) }}"
                               required
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm">
                        @error('name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nationality --}}
                    <div class="mb-4">
                        <label for="nationality" class="block text-sm font-medium text-gray-700">
                            Nationality
                        </label>
                        <input type="text"
                               name="nationality"
                               id="nationality"
                               value="{{ old('nationality', $artist->nationality) }}"
                               required
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm">
                        @error('nationality')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Image URL --}}
                    <div class="mb-4">
                        <label for="image" class="block text-sm font-medium text-gray-700">
                            Image URL
                        </label>
                        <input type="text"
                               name="image"
                               id="image"
                               value="{{ old('image', $artist->image) }}"
                               class="mt-1 block w-full rounded border-gray-300 shadow-sm">
                        @error('image')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700">
                            Description
                        </label>
                        <textarea name="description"
                                  id="description"
                                  rows="4"
                                  required
                                  class="mt-1 block w-full rounded border-gray-300 shadow-sm">{{ old('description', $artist->description) }}</textarea>
                        @error('description')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>


                    {{-- Actions --}}
                    <div class="flex justify-between">
                        <a href="{{ route('artistcrud.index') }}"
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
