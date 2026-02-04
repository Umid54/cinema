<x-guest-layout>
    <div class="relative w-full max-w-md">

        <div class="absolute inset-0 rounded-2xl blur-2xl
                    bg-gradient-to-br from-cyan-500/20 via-sky-500/10 to-emerald-500/20"></div>

        <div class="relative hud-glass p-8 rounded-2xl border border-cyan-400/30">

            <h1 class="text-center text-xl font-semibold text-white mb-6">
                Регистрация
            </h1>

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <div>
                    <x-input-label for="name" value="Имя" />
                    <x-text-input id="name" name="name" type="text" required autofocus />
                </div>

                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" name="email" type="email" required />
                </div>

                <div>
                    <x-input-label for="password" value="Пароль" />
                    <x-text-input id="password" name="password" type="password" required />
                </div>

                <div>
                    <x-input-label for="password_confirmation" value="Повторите пароль" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" required />
                </div>

                <x-primary-button class="w-full justify-center">
                    Зарегистрироваться
                </x-primary-button>
            </form>

            <div class="mt-6 text-center text-xs text-slate-400">
                🎁 Trial Premium на 24 часа после регистрации
            </div>

        </div>
    </div>
</x-guest-layout>
