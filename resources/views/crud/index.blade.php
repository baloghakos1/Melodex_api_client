<x-app-layout>
    <x-slot name="header">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="{{ asset('css/crudindex.css') }}">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crud') }}
        </h2>
        <form method="GET" action="{{ request()->url() }}" class="select_crud">
            <label for="crud" class="font-semibold">
                {{ __('Select Data table: ') }}
            </label>

            <select name="crud" id="crud" title="Data_table" 
                    class="border-gray-300 rounded-lg shadow-sm"
                    onchange="location = this.value">
                <option value="{{ route('crud.index') }}" selected>-- Data tables --</option>
                <option value="{{ route('crud.artists') }}">Artists</option>
                <option value="{{ route('crud.members') }}">Members</option>
                <option value="{{ route('crud.albums') }}">Albums</option>
                <option value="{{ route('crud.songs') }}">Songs</option>
            </select>
        </form>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">
                <label>
                    {{ __('No table selected') }}
                </label>
            </div>
        </div>
    </div>

</x-app-layout>