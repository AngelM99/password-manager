<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('Por favor, configura un PIN de 4 dígitos para mayor seguridad.') }}
    </div>

    <form method="POST" action="{{ route('set-pin.store') }}">
        @csrf

        <div>
            <x-input-label for="pin" :value="__('PIN (4 dígitos)')" />
            <input
                id="pin"
                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                type="password"
                name="pin"
                maxlength="4"
                pattern="[0-9]{4}"
                inputmode="numeric"
                placeholder="••••"
                required
                autofocus
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('pin')" class="mt-2" />
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Ingresa 4 números</p>
        </div>

        <div class="mt-4">
            <x-input-label for="pin_confirmation" :value="__('Confirmar PIN')" />
            <input
                id="pin_confirmation"
                class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                type="password"
                name="pin_confirmation"
                maxlength="4"
                pattern="[0-9]{4}"
                inputmode="numeric"
                placeholder="••••"
                required
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('pin_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button type="submit">
                {{ __('Guardar PIN') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>