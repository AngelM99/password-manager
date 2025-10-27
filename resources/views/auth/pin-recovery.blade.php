<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('¿Olvidaste tu PIN? Ingresa tu dirección de correo electrónico y te enviaremos un enlace para restablecerlo.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('success')" />

    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-800 dark:bg-red-900/50 dark:text-red-200">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('pin.recovery.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Correo Electrónico')" />
            <input
                id="email"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-6 flex items-center justify-between">
            <a href="{{ route('login') }}"
                class="text-sm text-gray-600 underline hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                {{ __('Volver al login') }}
            </a>

            <x-primary-button>
                {{ __('Enviar enlace') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
