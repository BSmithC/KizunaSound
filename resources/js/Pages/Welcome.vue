<script setup>
import MusicalNoteIcon from '@/Components/Icons/MusicalNoteIcon.vue';
import SpeakerWaveIcon from '@/Components/Icons/SpeakerWaveIcon.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>

    <Head title="KizunaSound - 音楽の絆" />
    <div class="min-h-screen bg-gray-200 selection:bg-red-600 selection:text-white">
        <!-- Japanese wave pattern background -->
        <div class="fixed inset-0 opacity-10">
            <div class="absolute inset-0 bg-[radial-gradient(black_1px,transparent_1px)] [background-size:16px_16px]">
            </div>
        </div>

        <!-- Decorative Japanese music elements -->
        <div class="fixed top-20 left-10 text-red-600 opacity-20 text-6xl rotate-12 hidden lg:block">
            <SpeakerWaveIcon />
        </div>
        <div class="fixed bottom-20 right-10 text-black opacity-10 text-8xl -rotate-12 hidden lg:block"></div>
        <div class="fixed top-1/3 right-20 text-red-600 opacity-15 text-5xl hidden lg:block">
            <MusicalNoteIcon />
        </div>
        <div class="fixed bottom-1/3 left-20 text-red-600 opacity-10 text-5xl hidden lg:block"></div>

        <div class="relative flex min-h-screen flex-col items-center justify-center">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <!-- Header with Japanese-inspired navigation -->
                <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3 border-b border-red-600/30">
                    <div class="flex lg:col-start-2 lg:justify-center">
                        <!-- KizunaSound Logo -->
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <div
                                    class="h-12 w-12 lg:h-16 lg:w-16 bg-red-600 rounded-full flex items-center justify-center shadow-lg">
                                    <span class="text-white text-2xl lg:text-3xl font-bold">絆</span>
                                </div>
                            </div>
                            <div class="text-left">
                                <div class="text-gray-900 font-bold text-xl lg:text-2xl tracking-wider">KizunaSound
                                </div>
                                <div class="text-red-600 text-xs lg:text-sm tracking-wide">音楽の絆 · Bonds Through Music
                                </div>
                            </div>
                        </div>
                    </div>
                    <nav v-if="canLogin" class="-mx-3 flex flex-1 justify-end gap-2">
                        <!-- Mostrar Dashboard si el usuario está autenticado -->
                        <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                            class="relative px-4 py-2 text-gray-700 font-medium transition-all duration-300 hover:text-red-600 group">
                            <span class="relative z-10">Dashboard</span>
                            <span
                                class="absolute inset-x-0 bottom-0 h-0.5 bg-red-600 scale-x-0 group-hover:scale-x-100 transition-transform origin-left"></span>
                        </Link>

                        <!-- Mostrar Login y Register si NO está autenticado -->
                        <template v-else>
                            <Link :href="route('login')"
                                class="relative px-4 py-2 text-gray-700 font-medium transition-all duration-300 hover:text-red-600 group">
                                <span class="relative z-10">Login</span>
                                <span
                                    class="absolute inset-x-0 bottom-0 h-0.5 bg-red-600 scale-x-0 group-hover:scale-x-100 transition-transform origin-left"></span>
                            </Link>
                            <Link v-if="canRegister" :href="route('register')"
                                class="relative px-5 py-2 bg-red-600 text-white font-medium rounded-md transition-all duration-300 hover:bg-red-700 hover:shadow-lg hover:shadow-red-600/30">
                                Register
                            </Link>
                        </template>
                    </nav>
                </header>

                <main class="mt-8">
                    <!-- Mostrar contenido diferente según autenticación -->
                    <template v-if="$page.props.auth.user">
                        <!-- Contenido para usuarios autenticados -->
                        <div class="text-center mb-12">
                            <div class="inline-flex items-center gap-2 text-red-600 mb-3">
                                <span class="text-sm">
                                    <SpeakerWaveIcon />
                                </span>
                                <span class="w-8 h-px bg-red-600/50"></span>
                                <span class="text-xs tracking-wider text-gray-600">BIENVENIDO</span>
                                <span class="w-8 h-px bg-red-600/50"></span>
                                <span class="text-sm">
                                    <MusicalNoteIcon />
                                </span>
                            </div>
                            <h1 class="text-5xl lg:text-7xl font-bold text-gray-900 mb-4 tracking-wider">
                                {{ $page.props.auth.user.name }}
                            </h1>
                            <p class="text-red-600 text-lg lg:text-xl tracking-wide">Bienvenido de vuelta a KizunaSound
                            </p>
                            <div class="flex justify-center gap-4 mt-4">
                                <div class="w-12 h-px bg-red-600/50"></div>
                                <div class="text-red-600">
                                    <MusicalNoteIcon />
                                </div>
                                <div class="w-12 h-px bg-red-600/50"></div>
                            </div>
                        </div>

                        <!-- Grid de contenido para usuarios autenticados -->
                        <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">
                            <!-- Mi Música Card -->
                            <div
                                class="group relative flex flex-col items-start gap-6 overflow-hidden rounded-lg bg-white p-6 transition-all duration-300 hover:shadow-xl hover:scale-[1.02] border border-gray-200 hover:border-red-600/50 cursor-pointer">
                                <div class="flex items-center gap-4 w-full">
                                    <div
                                        class="flex size-14 shrink-0 items-center justify-center rounded-full bg-red-600/10 group-hover:bg-red-600/20 transition-colors">
                                        <svg class="size-7 text-red-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2
                                            class="text-xl font-semibold text-gray-900 group-hover:text-red-600 transition-colors">
                                            Mi Música
                                        </h2>
                                        <p class="text-gray-500 text-sm">Tus playlists y canciones favoritas</p>
                                    </div>
                                    <svg class="size-5 shrink-0 stroke-red-600 ml-auto opacity-0 group-hover:opacity-100 transition-opacity"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-600 leading-relaxed">
                                    Accede a tus playlists personalizadas, canciones guardadas y recomendaciones basadas
                                    en tus gustos.
                                </p>
                            </div>

                            <!-- Mis Playlists Card -->
                            <div
                                class="group relative flex flex-col items-start gap-6 overflow-hidden rounded-lg bg-white p-6 transition-all duration-300 hover:shadow-xl hover:scale-[1.02] border border-gray-200 hover:border-red-600/50 cursor-pointer">
                                <div class="flex items-center gap-4 w-full">
                                    <div
                                        class="flex size-14 shrink-0 items-center justify-center rounded-full bg-red-600/10 group-hover:bg-red-600/20 transition-colors">
                                        <svg class="size-7 text-red-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 6h16M4 12h16M4 18h16"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2
                                            class="text-xl font-semibold text-gray-900 group-hover:text-red-600 transition-colors">
                                            Mis Playlists
                                        </h2>
                                        <p class="text-gray-500 text-sm">Gestiona tus listas de reproducción</p>
                                    </div>
                                    <svg class="size-5 shrink-0 stroke-red-600 ml-auto opacity-0 group-hover:opacity-100 transition-opacity"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-600 leading-relaxed">
                                    Crea, edita y comparte tus playlists con la comunidad. Organiza tu música por
                                    género, artista o estado de ánimo.
                                </p>
                            </div>

                            <!-- Actividad Reciente Card -->
                            <div
                                class="group relative flex flex-col items-start gap-6 overflow-hidden rounded-lg bg-white p-6 transition-all duration-300 hover:shadow-xl hover:scale-[1.02] border border-gray-200 hover:border-red-600/50 cursor-pointer">
                                <div class="flex items-center gap-4 w-full">
                                    <div
                                        class="flex size-14 shrink-0 items-center justify-center rounded-full bg-red-600/10 group-hover:bg-red-600/20 transition-colors">
                                        <svg class="size-7 text-red-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2
                                            class="text-xl font-semibold text-gray-900 group-hover:text-red-600 transition-colors">
                                            Actividad Reciente
                                        </h2>
                                        <p class="text-gray-500 text-sm">Lo que has escuchado</p>
                                    </div>
                                    <svg class="size-5 shrink-0 stroke-red-600 ml-auto opacity-0 group-hover:opacity-100 transition-opacity"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-600 leading-relaxed">
                                    Revisa tu historial de reproducción y descubre qué canciones han marcado tu semana.
                                </p>
                            </div>

                            <!-- Recomendaciones Card -->
                            <div
                                class="group relative flex flex-col items-start gap-6 overflow-hidden rounded-lg bg-white p-6 transition-all duration-300 hover:shadow-xl hover:scale-[1.02] border border-gray-200 hover:border-red-600/50 cursor-pointer">
                                <div class="flex items-center gap-4 w-full">
                                    <div
                                        class="flex size-14 shrink-0 items-center justify-center rounded-full bg-red-600/10 group-hover:bg-red-600/20 transition-colors">
                                        <svg class="size-7 text-red-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2
                                            class="text-xl font-semibold text-gray-900 group-hover:text-red-600 transition-colors">
                                            Recomendaciones
                                        </h2>
                                        <p class="text-gray-500 text-sm">Basado en tus gustos</p>
                                    </div>
                                    <svg class="size-5 shrink-0 stroke-red-600 ml-auto opacity-0 group-hover:opacity-100 transition-opacity"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-600 leading-relaxed">
                                    Descubre nueva música japonesa seleccionada especialmente para ti basada en tus
                                    preferencias.
                                </p>
                            </div>
                        </div>

                        <!-- Estadísticas del Usuario -->
                        <div
                            class="mt-12 p-6 rounded-lg bg-gradient-to-r from-red-600/10 to-transparent border-l-4 border-red-600 bg-white/50">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-red-600">0</div>
                                    <div class="text-sm text-gray-600">Canciones escuchadas</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-red-600">0</div>
                                    <div class="text-sm text-gray-600">Playlists creadas</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-red-600">0</div>
                                    <div class="text-sm text-gray-600">Artistas seguidos</div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template v-else>
                        <!-- Contenido para usuarios NO autenticados (visitantes) -->
                        <div class="text-center mb-12">
                            <div class="inline-flex items-center gap-2 text-red-600 mb-3">
                                <span class="text-sm">
                                    <SpeakerWaveIcon />
                                </span>
                                <span class="w-8 h-px bg-red-600/50"></span>
                                <span class="text-xs tracking-wider text-gray-600">KIZUNA</span>
                                <span class="w-8 h-px bg-red-600/50"></span>
                                <span class="text-sm">
                                    <MusicalNoteIcon />
                                </span>
                            </div>
                            <h1 class="text-5xl lg:text-7xl font-bold text-gray-900 mb-4 tracking-wider">
                                音楽で繋がる
                            </h1>
                            <p class="text-red-600 text-lg lg:text-xl tracking-wide">Connected Through Music</p>
                            <div class="flex justify-center gap-4 mt-4">
                                <div class="w-12 h-px bg-red-600/50"></div>
                                <div class="text-red-600">
                                    <MusicalNoteIcon />
                                </div>
                                <div class="w-12 h-px bg-red-600/50"></div>
                            </div>
                            <p class="text-gray-600 text-sm mt-6 max-w-md mx-auto">
                                KizunaSound es un espacio donde la música japonesa
                                y la tecnología se fusionan para crear experiencias únicas
                            </p>
                            <div class="flex justify-center gap-4 mt-8">
                                <Link :href="route('register')"
                                    class="px-6 py-3 bg-red-600 text-white font-medium rounded-md transition-all duration-300 hover:bg-red-700 hover:shadow-lg hover:shadow-red-600/30">
                                    Comienza ahora
                                </Link>
                                <Link :href="route('login')"
                                    class="px-6 py-3 border border-red-600 text-red-600 font-medium rounded-md transition-all duration-300 hover:bg-red-600 hover:text-white">
                                    Iniciar sesión
                                </Link>
                            </div>
                        </div>

                        <!-- Grid de contenido para visitantes -->
                        <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">
                            <!-- Music Discovery Card -->
                            <div
                                class="group relative flex flex-col items-start gap-6 overflow-hidden rounded-lg bg-white p-6 transition-all duration-300 hover:shadow-xl hover:scale-[1.02] border border-gray-200 hover:border-red-600/50 cursor-pointer">
                                <div class="absolute top-0 right-0 w-32 h-32 opacity-5">
                                    <div class="absolute top-0 right-0 w-16 h-16 border-t-2 border-r-2 border-red-600">
                                    </div>
                                </div>

                                <div class="flex items-center gap-4 w-full">
                                    <div
                                        class="flex size-14 shrink-0 items-center justify-center rounded-full bg-red-600/10 group-hover:bg-red-600/20 transition-colors">
                                        <svg class="size-7 text-red-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2
                                            class="text-xl font-semibold text-gray-900 group-hover:text-red-600 transition-colors">
                                            Descubre Música
                                        </h2>
                                        <p class="text-gray-500 text-sm">Explora nuevos sonidos japoneses</p>
                                    </div>
                                    <svg class="size-5 shrink-0 stroke-red-600 ml-auto opacity-0 group-hover:opacity-100 transition-opacity"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>

                                <p class="text-sm text-gray-600 leading-relaxed">
                                    Sumérgete en un mundo de melodías que conectan corazones.
                                    Desde J-Pop hasta música tradicional, encuentra tu próxima canción favorita.
                                </p>

                                <div class="flex gap-2 mt-2">
                                    <span class="text-xs px-2 py-1 rounded-full bg-red-600/10 text-red-600">J-Pop</span>
                                    <span class="text-xs px-2 py-1 rounded-full bg-red-600/10 text-red-600">Anime</span>
                                    <span
                                        class="text-xs px-2 py-1 rounded-full bg-red-600/10 text-red-600">Tradicional</span>
                                </div>
                            </div>

                            <!-- Community Card -->
                            <div
                                class="group relative flex flex-col items-start gap-6 overflow-hidden rounded-lg bg-white p-6 transition-all duration-300 hover:shadow-xl hover:scale-[1.02] border border-gray-200 hover:border-red-600/50 cursor-pointer">
                                <div class="absolute top-0 right-0 w-32 h-32 opacity-5">
                                    <div class="absolute top-0 right-0 w-16 h-16 border-t-2 border-r-2 border-red-600">
                                    </div>
                                </div>

                                <div class="flex items-center gap-4 w-full">
                                    <div
                                        class="flex size-14 shrink-0 items-center justify-center rounded-full bg-red-600/10 group-hover:bg-red-600/20 transition-colors">
                                        <svg class="size-7 text-red-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2
                                            class="text-xl font-semibold text-gray-900 group-hover:text-red-600 transition-colors">
                                            Comunidad
                                        </h2>
                                        <p class="text-gray-500 text-sm">Conecta con otros amantes de la música</p>
                                    </div>
                                    <svg class="size-5 shrink-0 stroke-red-600 ml-auto opacity-0 group-hover:opacity-100 transition-opacity"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>

                                <p class="text-sm text-gray-600 leading-relaxed">
                                    Comparte tus playlists, descubre nuevos artistas y crea lazos
                                    musicales con personas de todo el mundo. La música nos une.
                                </p>

                                <div class="flex gap-2 mt-2">
                                    <span class="text-xs px-2 py-1 rounded-full bg-red-600/10 text-red-600">Foros</span>
                                    <span
                                        class="text-xs px-2 py-1 rounded-full bg-red-600/10 text-red-600">Eventos</span>
                                    <span
                                        class="text-xs px-2 py-1 rounded-full bg-red-600/10 text-red-600">Colaboraciones</span>
                                </div>
                            </div>

                            <!-- Playlists Card -->
                            <div
                                class="group relative flex flex-col items-start gap-6 overflow-hidden rounded-lg bg-white p-6 transition-all duration-300 hover:shadow-xl hover:scale-[1.02] border border-gray-200 hover:border-red-600/50 cursor-pointer">
                                <div class="absolute top-0 right-0 w-32 h-32 opacity-5">
                                    <div class="absolute top-0 right-0 w-16 h-16 border-t-2 border-r-2 border-red-600">
                                    </div>
                                </div>

                                <div class="flex items-center gap-4 w-full">
                                    <div
                                        class="flex size-14 shrink-0 items-center justify-center rounded-full bg-red-600/10 group-hover:bg-red-600/20 transition-colors">
                                        <svg class="size-7 text-red-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 6h16M4 12h16M4 18h16"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2
                                            class="text-xl font-semibold text-gray-900 group-hover:text-red-600 transition-colors">
                                            Playlists
                                        </h2>
                                        <p class="text-gray-500 text-sm">Playlists colaborativas</p>
                                    </div>
                                    <svg class="size-5 shrink-0 stroke-red-600 ml-auto opacity-0 group-hover:opacity-100 transition-opacity"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>

                                <p class="text-sm text-gray-600 leading-relaxed">
                                    Crea y comparte playlists temáticas. Cada lista es una historia
                                    que conecta personas a través de la música.
                                </p>

                                <div class="flex gap-2 mt-2">
                                    <span
                                        class="text-xs px-2 py-1 rounded-full bg-red-600/10 text-red-600">Colaborativas</span>
                                    <span
                                        class="text-xs px-2 py-1 rounded-full bg-red-600/10 text-red-600">Personalizadas</span>
                                </div>
                            </div>

                            <!-- Events Card -->
                            <div
                                class="group relative flex flex-col items-start gap-6 overflow-hidden rounded-lg bg-white p-6 transition-all duration-300 hover:shadow-xl hover:scale-[1.02] border border-gray-200 hover:border-red-600/50 cursor-pointer">
                                <div class="absolute top-0 right-0 w-32 h-32 opacity-5">
                                    <div class="absolute top-0 right-0 w-16 h-16 border-t-2 border-r-2 border-red-600">
                                    </div>
                                </div>

                                <div class="flex items-center gap-4 w-full">
                                    <div
                                        class="flex size-14 shrink-0 items-center justify-center rounded-full bg-red-600/10 group-hover:bg-red-600/20 transition-colors">
                                        <svg class="size-7 text-red-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2
                                            class="text-xl font-semibold text-gray-900 group-hover:text-red-600 transition-colors">
                                            Eventos
                                        </h2>
                                        <p class="text-gray-500 text-sm">Conciertos y encuentros</p>
                                    </div>
                                    <svg class="size-5 shrink-0 stroke-red-600 ml-auto opacity-0 group-hover:opacity-100 transition-opacity"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>

                                <p class="text-sm text-gray-600 leading-relaxed">
                                    Descubre eventos musicales, conciertos virtuales y meetups
                                    para compartir tu pasión por la música japonesa.
                                </p>

                                <div class="flex gap-2 mt-2">
                                    <span
                                        class="text-xs px-2 py-1 rounded-full bg-red-600/10 text-red-600">Conciertos</span>
                                    <span
                                        class="text-xs px-2 py-1 rounded-full bg-red-600/10 text-red-600">Virtuales</span>
                                    <span
                                        class="text-xs px-2 py-1 rounded-full bg-red-600/10 text-red-600">Meetups</span>
                                </div>
                            </div>
                        </div>

                        <!-- Featured Artist Section -->
                        <div
                            class="mt-12 p-6 rounded-lg bg-gradient-to-r from-red-600/10 to-transparent border-l-4 border-red-600 bg-white/50">
                            <div class="flex items-center gap-4 flex-wrap">
                                <div class="flex items-center gap-3">
                                    <div class="text-red-600 text-2xl">
                                        <MusicalNoteIcon />
                                    </div>
                                    <div>
                                        <p class="text-gray-500 text-xs">ARTISTA DESTACADO</p>
                                        <p class="text-gray-900 font-semibold">Artista de la Semana</p>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <p class="text-gray-600 text-sm">Descubre nuevos talentos cada semana. La música
                                        japonesa tiene un nuevo hogar.</p>
                                </div>
                                <Link :href="route('register')"
                                    class="px-4 py-2 border border-red-600 text-red-600 rounded-md text-sm hover:bg-red-600 hover:text-white transition-all duration-300">
                                    Explorar →
                                </Link>
                            </div>
                        </div>
                    </template>
                </main>

                <footer class="py-16 text-center text-sm text-gray-500 border-t border-red-600/30 mt-8">
                    <div class="flex justify-center gap-6 mb-4">
                        <span class="text-red-600 hover:text-red-700 transition-colors cursor-pointer">
                            <SpeakerWaveIcon />
                        </span>
                        <span class="text-red-600 hover:text-red-700 transition-colors cursor-pointer">
                            <MusicalNoteIcon />
                        </span>
                        <span class="text-red-600 hover:text-red-700 transition-colors cursor-pointer">
                            <SpeakerWaveIcon />
                        </span>
                    </div>
                    <p class="text-gray-400 text-xs mb-2">KizunaSound · 音楽で繋がる</p>
                    <p>Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})</p>
                    <p class="mt-2 text-xs text-gray-400">© 2024 KizunaSound - Donde la música crea lazos</p>
                </footer>
            </div>
        </div>
    </div>
</template>
