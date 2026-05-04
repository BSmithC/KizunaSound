<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex">
                <h1 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Album
                </h1>
            </div>
        </template>
        <template #default>
            <div class="mx-auto max-w-7xl py-8">
                <div class="relative mx-64 mb-8">
                    <MagnifyingGlassIcon
                        class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
                    <Input class="w-full pl-10 pr-4 py-2 rounded-lg dark:text-gray-100" placeholder="Search ..." />
                </div>
                <div
                    class="overflow-hidden rounded-2xl shadow-md dark:bg-gray-800/90 mt-4 backdrop-blur-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between rounded-xl mb-4 gap-4 px-6 mt-9">

                        <div>
                            <h1 class="flex text-xl justify-center font-semibold text-gray-800 dark:text-gray-200">
                                Recent Albums
                            </h1>
                            <div class="grid grid-cols-3 gap-4 mb-2 rounded-xl">
                                <div>
                                    <article
                                        class="overflow-hidden rounded-xl shadow-sm transition hover:shadow-lg hover:scale-[1.02] duration-200 bg-gradient-to-br from-gray-500 to-gray-600 dark:from-red-800 dark:to-red-900 p-5 border border-gray-400 dark:border-red-700">
                                        <img alt=""
                                            src="https://s.france24.com/media/display/c5b34a38-0c37-11ec-9db2-005056a97e36/w:1280/p:4x3/ABBA.jpg"
                                            class="h-44 w-44 mx-auto rounded-full object-cover border-2 dark:border-red-500">
                                        <div class="mt-4 text-center">
                                            <h2
                                                class="text-sm uppercase tracking-wide text-amber-300 dark:text-gray-900 font-semibold">
                                                Artist
                                            </h2>
                                            <p class="text-lg font-bold text-white mt-0">Abba</p>
                                            <h2
                                                class="text-sm uppercase tracking-wide text-amber-300 dark:text-gray-900 font-semibold mt-2">
                                                Music
                                            </h2>
                                            <p class="text-md text-gray-100 mt-0">When I Kissed The Teacher</p>
                                        </div>
                                    </article>
                                </div>


                            </div>
                        </div>

                        <div>
                            <h1
                                class="flex text-xl justify-center font-semibold text-gray-800 dark:text-gray-200 place-items-center">
                                Recent Albums
                            </h1>
                            <div class="grid grid-cols-3 gap-4 mb-4 px-6 rounded-xl">

                                <div>
                                    <article
                                        class="overflow-hidden rounded-xl shadow-sm transition hover:shadow-lg hover:scale-[1.02] duration-200 bg-gradient-to-br from-gray-500 to-gray-600 dark:from-red-800 dark:to-red-900 p-5 border border-gray-400 dark:border-red-700">
                                        <img alt=""
                                            src="https://s.france24.com/media/display/c5b34a38-0c37-11ec-9db2-005056a97e36/w:1280/p:4x3/ABBA.jpg"
                                            class="h-44 w-44 mx-auto rounded-full object-cover border-2 dark:border-red-500">
                                        <div class="mt-4 text-center">
                                            <h2
                                                class="text-sm uppercase tracking-wide text-amber-300 dark:text-gray-900 font-semibold">
                                                Artist
                                            </h2>
                                            <p class="text-lg font-bold text-white mt-0">Abba</p>
                                            <h2
                                                class="text-sm uppercase tracking-wide text-amber-300 dark:text-gray-900 font-semibold mt-2">
                                                Music
                                            </h2>
                                            <p class="text-md text-gray-100 mt-0">When I Kissed The Teacher</p>
                                        </div>
                                    </article>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div>
                        <h1 class="flex text-xl font-semibold justify-center text-gray-800 dark:text-gray-200">
                            Popular Albums
                        </h1>
                        <div class="flex justify-center rounded-xl mb-4 gap-4 px-6">
                            <div class="grid grid-cols-5 gap-4 mb-2 rounded-xl">

                                <div>
                                    <article
                                        class="overflow-hidden rounded-xl shadow-sm transition hover:shadow-lg hover:scale-[1.02] duration-200 bg-gradient-to-br from-gray-500 to-gray-600 dark:from-red-800 dark:to-red-900 p-5 border border-gray-400 dark:border-red-700">
                                        <img alt=""
                                            src="https://s.france24.com/media/display/c5b34a38-0c37-11ec-9db2-005056a97e36/w:1280/p:4x3/ABBA.jpg"
                                            class="h-44 w-44 mx-auto rounded-full object-cover border-2 dark:border-red-500">
                                        <div class="mt-4 text-center">
                                            <h2
                                                class="text-sm uppercase tracking-wide text-amber-300 dark:text-gray-900 font-semibold">
                                                Artist
                                            </h2>
                                            <p class="text-lg font-bold text-white mt-0">Abba</p>
                                            <h2
                                                class="text-sm uppercase tracking-wide text-amber-300 dark:text-gray-900 font-semibold mt-2">
                                                Music
                                            </h2>
                                            <p class="text-md text-gray-100 mt-0">When I Kissed The Teacher</p>
                                        </div>
                                    </article>
                                </div>
                                <div>
                                    <article
                                        class="overflow-hidden rounded-xl shadow-sm transition hover:shadow-lg hover:scale-[1.02] duration-200 bg-gradient-to-br from-gray-500 to-gray-600 dark:from-red-800 dark:to-red-900 p-5 border border-gray-400 dark:border-red-700">
                                        <img alt=""
                                            src="https://s.france24.com/media/display/c5b34a38-0c37-11ec-9db2-005056a97e36/w:1280/p:4x3/ABBA.jpg"
                                            class="h-44 w-44 mx-auto rounded-full object-cover border-2 dark:border-red-500">
                                        <div class="mt-4 text-center">
                                            <h2
                                                class="text-sm uppercase tracking-wide text-amber-300 dark:text-gray-900 font-semibold">
                                                Artist
                                            </h2>
                                            <p class="text-lg font-bold text-white mt-0">Abba</p>
                                            <h2
                                                class="text-sm uppercase tracking-wide text-amber-300 dark:text-gray-900 font-semibold mt-2">
                                                Music
                                            </h2>
                                            <p class="text-md text-gray-100 mt-0">When I Kissed The Teacher</p>
                                        </div>
                                    </article>
                                </div>
                                <div>
                                    <article
                                        class="overflow-hidden rounded-xl shadow-sm transition hover:shadow-lg hover:scale-[1.02] duration-200 bg-gradient-to-br from-gray-500 to-gray-600 dark:from-red-800 dark:to-red-900 p-5 border border-gray-400 dark:border-red-700">
                                        <img alt=""
                                            src="https://s.france24.com/media/display/c5b34a38-0c37-11ec-9db2-005056a97e36/w:1280/p:4x3/ABBA.jpg"
                                            class="h-44 w-44 mx-auto rounded-full object-cover border-2 dark:border-red-500">
                                        <div class="mt-4 text-center">
                                            <h2
                                                class="text-sm uppercase tracking-wide text-amber-300 dark:text-gray-900 font-semibold">
                                                Artist
                                            </h2>
                                            <p class="text-lg font-bold text-white mt-0">Abba</p>
                                            <h2
                                                class="text-sm uppercase tracking-wide text-amber-300 dark:text-gray-900 font-semibold mt-2">
                                                Music
                                            </h2>
                                            <p class="text-md text-gray-100 mt-0">When I Kissed The Teacher</p>
                                        </div>
                                    </article>
                                </div>
                                <div>
                                    <article
                                        class="overflow-hidden rounded-xl shadow-sm transition hover:shadow-lg hover:scale-[1.02] duration-200 bg-gradient-to-br from-gray-500 to-gray-600 dark:from-red-800 dark:to-red-900 p-5 border border-gray-400 dark:border-red-700">
                                        <img alt=""
                                            src="https://s.france24.com/media/display/c5b34a38-0c37-11ec-9db2-005056a97e36/w:1280/p:4x3/ABBA.jpg"
                                            class="h-44 w-44 mx-auto rounded-full object-cover border-2 dark:border-red-500">
                                        <div class="mt-4 text-center">
                                            <h2
                                                class="text-sm uppercase tracking-wide text-amber-300 dark:text-gray-900 font-semibold">
                                                Artist
                                            </h2>
                                            <p class="text-lg font-bold text-white mt-0">Abba</p>
                                            <h2
                                                class="text-sm uppercase tracking-wide text-amber-300 dark:text-gray-900 font-semibold mt-2">
                                                Music
                                            </h2>
                                            <p class="text-md text-gray-100 mt-0">When I Kissed The Teacher</p>
                                        </div>
                                    </article>
                                </div>
                                <div>
                                    <article
                                        class="overflow-hidden rounded-xl shadow-sm transition hover:shadow-lg hover:scale-[1.02] duration-200 bg-gradient-to-br from-gray-500 to-gray-600 dark:from-red-800 dark:to-red-900 p-5 border border-gray-400 dark:border-red-700">
                                        <img alt=""
                                            src="https://s.france24.com/media/display/c5b34a38-0c37-11ec-9db2-005056a97e36/w:1280/p:4x3/ABBA.jpg"
                                            class="h-44 w-44 mx-auto rounded-full object-cover border-2 dark:border-red-500">
                                        <div class="mt-4 text-center">
                                            <h2
                                                class="text-sm uppercase tracking-wide text-amber-300 dark:text-gray-900 font-semibold">
                                                Artist
                                            </h2>
                                            <p class="text-lg font-bold text-white mt-0">Abba</p>
                                            <h2
                                                class="text-sm uppercase tracking-wide text-amber-300 dark:text-gray-900 font-semibold mt-2">
                                                Music
                                            </h2>
                                            <p class="text-md text-gray-100 mt-0">When I Kissed The Teacher</p>
                                        </div>
                                    </article>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </AuthenticatedLayout>
</template>

<script>
import MagnifyingGlassIcon from '@/Components/Icons/MagnifyingGlassIcon.vue';
import Input from '@/Components/ui/input/Input.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

export default {
    components: {
        AuthenticatedLayout,
        MagnifyingGlassIcon,
        Input
    },
}
</script>
