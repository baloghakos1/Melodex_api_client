<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('image/angled_view.png') }}" alt="Melodex" class="h-9 w-auto">
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('artists.index')" :active="request()->routeIs('artists.index')">
                        {{ __('Artists') }}
                    </x-nav-link>
                    <x-nav-link :href="route('crud.index')" :active="request()->routeIs('crud.index')">
                        {{ __('Crud') }}
                    </x-nav-link>
                </div>

                <!-- Search Bar -->
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <div class="relative" id="searchContainer">
                        <form action="{{ route('search') }}" method="GET" class="flex items-center">
                            <input 
                                type="text" 
                                name="q" 
                                id="searchInput"
                                placeholder="Search..." 
                                autocomplete="off"
                                class="w-48 lg:w-64 px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            >
                            <button type="submit" class="ms-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        </form>

                        <div id="searchPopup" class="hidden absolute top-full right-0 mt-2 w-[350px] bg-white border border-gray-200 rounded-lg shadow-2xl z-[100] overflow-hidden">
                            <div id="popupContent" class="p-2 max-h-96 overflow-y-auto text-gray-900">
                                </div>
                        </div>
                    </div>
                </div>
                @push('scripts')
                <script>
                    const searchInput = document.getElementById('searchInput');
                    const searchPopup = document.getElementById('searchPopup');
                    const popupContent = document.getElementById('popupContent');
                    let debounceTimer;

                    searchInput.addEventListener('input', function() {
                        clearTimeout(debounceTimer);
                        const query = this.value;

                        if (query.length < 2) {
                            searchPopup.classList.add('hidden');
                            return;
                        }

                        debounceTimer = setTimeout(() => {
                            fetch(`/search/preview?q=${query}`)
                                .then(res => res.json())
                                .then(data => {
                                    if (data.length > 0) {
                                        let html = '';
                                        data.forEach(item => {
                                            const cover = item.searchable.cover || 'https://via.placeholder.com/40';
                                            
                                            
                                            //let targetUrl = '#';
                                            let targetUrl = `/artist/${item.searchable.artist_id}/${item.searchable.id}`;


                                            if (item.type === 'albums') {
                                                targetUrl = `/artist/${item.searchable.artist_id}/${item.searchable.id}`;
                                            } else if (item.type === 'artists') {
                                                targetUrl = `/artist/${item.searchable.id}`;
                                            }
                                            console.log("Full Item Object:", item); // Check if searchable exists
                                            console.log("Artist ID:", item.searchable?.id); // Check if this is a number/string
    
                                            console.log("Generated Link:", targetUrl);

                                            html += `
                                                <a href="${targetUrl}" class="flex items-center p-2 hover:bg-gray-100 cursor-pointer rounded-md transition mb-1 no-underline text-current">
                                                    <img src="${cover}" class="w-10 h-10 rounded object-cover mr-3" alt="${item.title} cover">
                                                    <div class="flex flex-col">
                                                        <span class="text-sm font-semibold text-gray-800">${item.title}</span>
                                                        <span class="text-xs text-gray-500 uppercase">${item.type}</span>
                                                    </div>
                                                </a>`;
                                        });
                                        popupContent.innerHTML = html;
                                        searchPopup.classList.remove('hidden');
                                    } else {
                                        popupContent.innerHTML = '<p class="text-xs text-gray-400 p-2">No matches found.</p>';
                                        searchPopup.classList.remove('hidden');
                                    }
                                });
                        }, 300); 
                    });

                    document.addEventListener('click', (e) => {
                        if (!document.getElementById('searchContainer').contains(e.target)) {
                            searchPopup.classList.add('hidden');
                        }
                    });
                </script>
                @endpush
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Search -->
        <div class="px-4 py-3 border-t border-gray-200">
            <form action="{{ route('search') }}" method="GET" class="flex items-center gap-2">
                <input type="text" name="q" placeholder="Search..." class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <button type="submit" class="px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </form>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
