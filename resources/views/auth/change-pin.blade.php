<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('Cambia tu PIN de seguridad ingresando tu PIN actual y el nuevo PIN de 4 dígitos.') }}
    </div>

    <!-- Validation Errors -->
    <x-auth-session-status class="mb-4" :status="session('success')" />

    <form method="POST" action="{{ route('pin.update') }}">
        @csrf

        <!-- Current PIN -->
        <div>
            <x-input-label for="current_pin" :value="__('PIN Actual')" />
            <input
                id="current_pin"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                type="password"
                name="current_pin"
                maxlength="4"
                pattern="[0-9]{4}"
                inputmode="numeric"
                placeholder="••••"
                required
                autofocus />
            <x-input-error :messages="$errors->get('current_pin')" class="mt-2" />
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
            <x-input-label for="pin_confirmation" :value="__('Confirmar Nuevo PIN')" />
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

        <div class="mt-6 flex items-center justify-between">
            <a href="{{ route('dashboard') }}"
                class="text-sm text-gray-600 underline hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                {{ __('Cancelar') }}
            </a>

            <x-primary-button>
                {{ __('Cambiar PIN') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
