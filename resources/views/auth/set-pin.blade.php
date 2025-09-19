<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Por favor, configura un PIN de 4 dígitos para mayor seguridad.') }}
    </div>

    <form method="POST" action="{{ route('set-pin.store') }}">
        @csrf

        <div>
            <x-input-label for="pin" :value="__('PIN')" />
            <x-text-input id="pin" class="block mt-1 w-full" type="password" name="pin" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('pin')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="pin_confirmation" :value="__('Confirmar PIN')" />
            <x-text-input id="pin_confirmation" class="block mt-1 w-full" type="password" name="pin_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('pin_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button type="submit">
                {{ __('Guardar PIN') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>