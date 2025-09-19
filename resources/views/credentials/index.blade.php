<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mis Credenciales') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('status'))
                        <div class="mb-4 text-green-600">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('credentials.store') }}">
                        @csrf
                        <div>
                            <x-input-label for="title" :value="__('Título')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" required />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="username" :value="__('Nombre de usuario')" />
                            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" required />
                            <x-input-error :messages="$errors->get('username')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="password" :value="__('Contraseña')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="url" :value="__('URL (opcional)')" />
                            <x-text-input id="url" class="block mt-1 w-full" type="text" name="url" />
                            <x-input-error :messages="$errors->get('url')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button type="submit">{{ __('Guardar Credencial') }}</x-primary-button>
                        </div>
                    </form>

                    <h3 class="mt-6 text-lg font-medium">Credenciales guardadas:</h3>
                    @forelse ($credentials as $credential)
                        <div class="mt-2 p-2 border rounded">
                            <strong>{{ $credential->title }}</strong><br>
                            Usuario: {{ $credential->username }}<br>
                            URL: {{ $credential->url ?? 'N/A' }}
                        </div>
                    @empty
                        <p class="mt-2">No hay credenciales guardadas.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>