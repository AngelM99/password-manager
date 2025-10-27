<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('Ingresa tu nuevo PIN de 4 dígitos para restablecer tu acceso.') }}
    </div>

    <form method="POST" action="{{ route('pin.reset.store') }}">
        @csrf

        <!-- Token -->
        <input type="hidden" name="token" value="{{ $token }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Correo Electrónico')" />
            <input
                id="email"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                type="email"
                name="email"
                value="{{ old('email', $email ?? '') }}"
                required
                autofocus
                autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- New PIN -->
        <div class="mt-4">
            <x-input-label for="pin" :value="__('Nuevo PIN (4 dígitos)')" />
            <input
                id="pin"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                type="password"
                name="pin"
                maxlength="4"
                pattern="[0-9]{4}"
                inputmode="numeric"
                placeholder="••••"
                required />
            <x-input-error :messages="$errors->get('pin')" class="mt-2" />
        </div>

        <!-- Confirm PIN -->
        <div class="mt-4">
            <x-input-label for="pin_confirmation" :value="__('Confirmar PIN')" />
            <input
                id="pin_confirmation"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                type="password"
                name="pin_confirmation"
                maxlength="4"
                pattern="[0-9]{4}"
                inputmode="numeric"
                placeholder="••••"
                required />
            <x-input-error :messages="$errors->get('pin_confirmation')" class="mt-2" />
        </div>

        <x-input-error :messages="$errors->get('token')" class="mt-2" />

        <div class="mt-6 flex items-center justify-end">
            <x-primary-button>
                {{ __('Restablecer PIN') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
