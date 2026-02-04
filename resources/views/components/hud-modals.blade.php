{{-- LOGIN MODAL --}}
<div id="loginModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur">

    <div class="hud-card max-w-md w-full p-6">
        <h2 class="text-xl font-bold text-cyan-400 mb-4">
            🔒 Требуется вход
        </h2>

        <p class="text-neutral-300 mb-6">
            Чтобы смотреть видео, войдите или зарегистрируйтесь.
        </p>

        <div class="flex gap-4">
            <a href="{{ route('login') }}" class="btn btn-cyan w-full text-center">
                Войти
            </a>
            <a href="{{ route('register') }}" class="btn btn-outline w-full text-center">
                Регистрация
            </a>
        </div>

        <button onclick="closeHudModal('loginModal')"
                class="absolute top-3 right-3 text-neutral-400 hover:text-white">
            ✕
        </button>
    </div>
</div>

{{-- PREMIUM MODAL --}}
<div id="premiumModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur">

    <div class="hud-card max-w-md w-full p-6">
        <h2 class="text-xl font-bold text-yellow-400 mb-4">
            ⭐ Premium доступ
        </h2>

        <p class="text-neutral-300 mb-6">
            Этот фильм доступен только для Premium-пользователей.
        </p>

        <a href="{{ route('premium.index') }}"
           class="btn btn-yellow w-full text-center">
            Оформить Premium
        </a>

        <button onclick="closeHudModal('premiumModal')"
                class="absolute top-3 right-3 text-neutral-400 hover:text-white">
            ✕
        </button>
    </div>
</div>
