<nav x-data="{ open: false }" class="bg-neutral-950 border-b border-neutral-800">
    <!-- Primary Navigation -->
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex justify-between h-16 items-center">

            <!-- LEFT -->
            <div class="flex items-center gap-10">

                <!-- LOGO -->
                <a href="{{ route('home') }}" class="flex items-center gap-2 text-xl font-bold text-white">
                    🎬 <span>{{ config('app.name', 'Cinema') }}</span>
                </a>

                <!-- MAIN MENU -->
                <div class="hidden md:flex gap-6 text-sm font-medium">
                    <a href="{{ route('movies.index') }}"
                       class="{{ request()->is('movies*') ? 'text-cyan-400' : 'text-neutral-300 hover:text-white' }}">
                        Фильмы
                    </a>

                    <a href="{{ route('series.index') }}"
                       class="{{ request()->is('series*') ? 'text-cyan-400' : 'text-neutral-300 hover:text-white' }}">
                        Сериалы
                    </a>

                    <a href="{{ route('anime.index') }}"
                       class="{{ request()->is('anime') ? 'text-cyan-400' : 'text-neutral-300 hover:text-white' }}">
                        Аниме
                    </a>

                    <a href="{{ route('cartoons.index') }}"
                       class="{{ request()->is('cartoons') ? 'text-cyan-400' : 'text-neutral-300 hover:text-white' }}">
                        Мультфильмы
                    </a>

                    <a href="{{ route('documentary.index') }}"
                       class="{{ request()->is('documentary') ? 'text-cyan-400' : 'text-neutral-300 hover:text-white' }}">
                        Документальные
                    </a>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="flex items-center gap-4">

                <!-- SEARCH (visual, ready for future) -->
                <div class="hidden md:block">
                    <input
                        type="text"
                        placeholder="Поиск…"
                        class="bg-neutral-900 border border-neutral-700 rounded-md px-3 py-1.5 text-sm text-neutral-200 placeholder-neutral-500 focus:outline-none focus:border-cyan-400"
                    >
                </div>

                <!-- AUTH -->
                @auth
                    <div class="relative">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="flex items-center gap-2 text-sm text-neutral-300 hover:text-white">
                                    {{ Auth::user()->name }}
                                    <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('account.index')">
                                    Аккаунт
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('favorites.index')">
                                    Избранное
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('history.index')">
                                    История
                                </x-dropdown-link>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link
                                        :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                        Выйти
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                       class="text-sm text-neutral-300 hover:text-white">
                        Войти
                    </a>
                @endauth

                <!-- MOBILE BUTTON -->
                <button @click="open = !open" class="md:hidden text-neutral-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- MOBILE MENU -->
    <div x-show="open" x-transition class="md:hidden border-t border-neutral-800 bg-neutral-950">
        <div class="px-6 py-4 space-y-3 text-sm">
            <a href="{{ route('movies.index') }}" class="block text-neutral-300 hover:text-white">Фильмы</a>
            <a href="{{ route('series.index') }}" class="block text-neutral-300 hover:text-white">Сериалы</a>
            <a href="{{ route('anime.index') }}" class="block text-neutral-300 hover:text-white">Аниме</a>
            <a href="{{ route('cartoons.index') }}" class="block text-neutral-300 hover:text-white">Мультфильмы</a>
            <a href="{{ route('documentary.index') }}" class="block text-neutral-300 hover:text-white">Документальные</a>

            @auth
                <hr class="border-neutral-800">
                <a href="{{ route('account.index') }}" class="block text-neutral-300 hover:text-white">Аккаунт</a>
                <a href="{{ route('favorites.index') }}" class="block text-neutral-300 hover:text-white">Избранное</a>
                <a href="{{ route('history.index') }}" class="block text-neutral-300 hover:text-white">История</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-left w-full text-neutral-300 hover:text-white">
                        Выйти
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block text-neutral-300 hover:text-white">Войти</a>
            @endauth
        </div>
    </div>
</nav>
