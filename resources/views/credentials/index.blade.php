<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-24 sm:px-6 sm:py-32 lg:px-8">
        <div class="mx-auto max-w-2xl">
            <form>
                <div class="space-y-12">
                    <!-- Profile Section -->
                    <div class="border-b border-white/10 pb-12">
                        <div class="mt-10 grid grid-cols-1 gap-6">
                            <div>
                                <h2 class="text-base/7 font-semibold text-white">Personal Information</h2>
                                <p class="mt-1 text-sm/6 text-gray-400">Use a permanent address where you can receive
                                    mail.
                                </p>
                            </div>
                            <!-- Username -->
                            <div>
                                <label for="first-name" class="block text-sm/6 font-medium text-white">First
                                    name</label>
                                <div class="mt-2">
                                    <input id="first-name" type="text" name="first-name" autocomplete="given-name"
                                        class="block w-full rounded-md bg-white/5 px-3 py-1.5 text-base text-white outline-1 -outline-offset-1 outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6">
                                </div>
                            </div>
                            <!-- Last Name -->
                            <div>
                                <label for="last-name" class="block text-sm/6 font-medium text-white">Last name</label>
                                <div class="mt-2">
                                    <input id="last-name" type="text" name="last-name" autocomplete="family-name"
                                        class="block w-full rounded-md bg-white/5 px-3 py-1.5 text-base text-white outline-1 -outline-offset-1 outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6">
                                </div>
                            </div>

                            <!-- About -->
                            <div>
                                <label for="about" class="block text-sm/6 font-medium text-white">About</label>
                                <div class="mt-2">
                                    <textarea id="about" name="about" rows="3"
                                        class="block w-full rounded-md bg-white/5 px-3 py-1.5 text-base text-white outline-1 -outline-offset-1 outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6"></textarea>
                                </div>
                                <p class="mt-3 text-sm/6 text-gray-400">Write a few sentences about yourself.</p>
                            </div>
                        </div>
                    </div>
                    <!-- Form Actions -->
                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <button type="button" class="text-sm/6 font-semibold text-white">Cancel</button>
                        <button type="button"
                            class="rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
