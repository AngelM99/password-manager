<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-24 sm:px-6 sm:py-32 lg:px-8">
        <div class="mx-auto max-w-2xl">
            <form>
                <div class="space-y-12">
                    <!-- Profile Section -->
                    <div class="border-b border-white/10 pb-12">
                        <div class="mt-10 grid grid-cols-1 gap-6">
                            <div>
                                <h2 class="text-base/7 font-semibold text-white">Registrar Nueva Credencial</h2>
                                <p class="mt-1 text-sm/6 text-gray-400">Guarda de forma segura tus accesos a plataformas y servicios.
                                </p>
                            </div>
                            <!-- Plataforma -->
                            <div>
                                <label for="platform_name" class="block text-sm/6 font-medium text-white">Nombre de la plataforma/servicio</label>
                                <div class="mt-2">
                                    <input id="platform_name" type="text" name="platform_name" autocomplete="given-name"
                                        class="block w-full rounded-md bg-white/5 px-3 py-1.5 text-base text-white outline-1 -outline-offset-1 outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6" placeholder="Ej: Facebook, Twitter, Gmail...">
                                </div>
                            </div>
                            <!-- Usuario -->
                            <div>
                                <label for="platform_username" class="block text-sm/6 font-medium text-white">Usuario o correo asociado</label>
                                <div class="mt-2">
                                    <input id="platform_username" type="text" name="platform_username" autocomplete="family-name"
                                        class="block w-full rounded-md bg-white/5 px-3 py-1.5 text-base text-white outline-1 -outline-offset-1 outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6" placeholder="Ej: usuario@gmail.com">
                                </div>
                            </div>
                            <!-- Contraseña -->
                            <div>
                                <label for="platform_password" class="block text-sm/6 font-medium text-white">Contraseña</label>
                                <div class="mt-2">
                                    <input id="platform_password" type="password" name="platform_password" autocomplete="family-name"
                                        class="block w-full rounded-md bg-white/5 px-3 py-1.5 text-base text-white outline-1 -outline-offset-1 outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6" placeholder="********">
                                </div>
                            </div>
                            <!-- Nota opcional -->
                            <div>
                                <label for="platform_note" class="block text-sm/6 font-medium text-white">Nota opcional</label>
                                <div class="mt-2">
                                    <textarea id="platform_note" name="platform_note" rows="3"
                                        class="block w-full rounded-md bg-white/5 px-3 py-1.5 text-base text-white outline-1 -outline-offset-1 outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6" placeholder="Ej: Esta cuenta es de trabajo / renovar cada 3 meses."></textarea>
                                </div>
                                <p class="mt-3 text-sm/6 text-gray-400">Campo adicional para recordar detalles de la credencial (opcional).</p>
                            </div>
                        </div>
                    </div>
                    <!-- Form Actions -->
                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <button type="button" class="text-sm/6 font-semibold text-white">Cancelar</button>
                        <button type="button"
                            class="rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
