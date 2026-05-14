<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex-1">
                <h1 class="text-xl font-bold leading-tight text-gray-800 dark:text-gray-200">
                    Music Library
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Discover and manage your favorite tracks</p>
            </div>
            <Button as-child>
                <Link href="/songs/create">
                <AddIcon class="w-5 h-5 mr-2" />
                New Music
                </Link>
            </Button>
        </template>

        <template #default>

            <div class="mt-4 mx-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="w-full sm:max-w-sm">
                    <Input v-model="search" placeholder="Filter Gender..." />
                </div>
            </div>
            <div class="mx-auto max-w-7xl py-8 px-4 sm:px-6 lg:px-8">
                <div class="space-y-12">
                    <div class="relative">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Recent Music</h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Fresh tracks just for you</p>
                            </div>
                            <div
                                class="h-px flex-1 mx-4 bg-gradient-to-r from-transparent via-gray-300 to-transparent dark:via-gray-700">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            <div v-for="song in songs.data" :key="song.id" class="group">
                                <div
                                    class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800/50 dark:to-gray-900/50 backdrop-blur-sm border border-gray-200 dark:border-gray-700 transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
                                    <div class="relative overflow-hidden">
                                        <img :src="song.thumbnail
                                            ? `/storage/${song.thumbnail}`
                                            : 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=400&h=400&fit=crop'"
                                            alt="Thumbnail"
                                            class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-110">
                                        <div
                                            class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        </div>
                                        <Button @click="playSong(song)" type="button"
                                            class="absolute bottom-4 right-4 p-3 bg-white rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 hover:scale-110">
                                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8 5v14l11-7z" />
                                            </svg>
                                        </Button>
                                    </div>
                                    <div class="p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span
                                                class="text-xs font-semibold text-gray-800 dark:text-gray-200 uppercase tracking-wider">{{
                                                    song.name }}</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400"></span>
                                        </div>
                                        <h3
                                            class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-1 line-clamp-1">
                                            {{ song.artist.name }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-1">{{
                                            song.gender.name }}</p>

                                        <div
                                            class="flex items-center justify-between mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                            <div class="flex items-center space-x-1">
                                                <span class="text-xs text-gray-800 dark:text-gray-200">--,--</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <audio ref="audioPlayer" preload="metadata"></audio>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mx-auto max-w-7xl py-8 px-4 sm:px-6 lg:px-8">
                <div class="space-y-12">
                    <div class="relative">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Popular Music</h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Fresh tracks just for you</p>
                            </div>
                            <div
                                class="h-px flex-1 mx-4 bg-gradient-to-r from-transparent via-gray-300 to-transparent dark:via-gray-700">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            <div v-for="song in songs.data" :key="song.id" class="group">
                                <div
                                    class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800/50 dark:to-gray-900/50 backdrop-blur-sm border border-gray-200 dark:border-gray-700 transition-all duration-300 hover:shadow-2xl hover:-translate-y-1">
                                    <div class="relative overflow-hidden">
                                        <img :src="song.thumbnail
                                            ? `/storage/${song.thumbnail}`
                                            : 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=400&h=400&fit=crop'"
                                            alt="Thumbnail"
                                            class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-110">
                                        <div
                                            class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        </div>
                                        <Button @click="playSong(song)" type="button"
                                            class="absolute bottom-4 right-4 p-3 bg-white rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 hover:scale-110">
                                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8 5v14l11-7z" />
                                            </svg>
                                        </Button>
                                    </div>
                                    <div class="p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span
                                                class="text-xs font-semibold text-gray-800 dark:text-gray-200 uppercase tracking-wider">{{
                                                    song.name }}</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400"></span>
                                        </div>
                                        <h3
                                            class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-1 line-clamp-1">
                                            {{ song.artist.name }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-1">{{
                                            song.gender.name }}</p>

                                        <div
                                            class="flex items-center justify-between mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                            <div class="flex items-center space-x-1">
                                                <span class="text-xs text-gray-800 dark:text-gray-200">--,--</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <audio ref="audioPlayer" preload="metadata"></audio>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </AuthenticatedLayout>
</template>

<script>
import AddIcon from '@/Components/Icons/AddIcon.vue';
import MagnifyingGlassIcon from '@/Components/Icons/MagnifyingGlassIcon.vue';
import Button from '@/Components/ui/button/Button.vue';
import Input from '@/Components/ui/input/Input.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, router } from '@inertiajs/vue3';

export default {
    components: {
        AuthenticatedLayout,
        MagnifyingGlassIcon,
        Input,
        Button,
        AddIcon,
        Link
    },
    props: {
        songs: Object,
        filters: Object,
    },
    data() {
        return {
            search: this.filters?.search || '',
            currentPlayingSong: null,
        }
    },
    mounted() {
        // Pasar la lista de canciones al player global
        if (this.$parent && this.$parent.player) {
            this.$parent.player.setSongsList(this.songs.data || [])
        }
    },
    computed: {
        allSongs() {
            return this.songs.data || [];
        }
    },
    watch: {
        search() {
            this.applyFilters();
        }
    },
    methods: {
        applyFilters() {
            router.get(
                '/songs',
                {
                    search: this.search
                },
                {
                    preserveState: true,
                    replace: true,
                    preserveScroll: true,
                }
            )
        },
        async playSong(song) {
            try {
                const audio = this.$refs.audioPlayer;

                // Si ya está reproduciendo otra canción, detenerla
                audio.pause();
                audio.currentTime = 0;

                // Asignar la URL del backend
                audio.src = route('songs.stream', song.id);

                // Guardar la canción actual
                this.currentPlayingSong = song;

                // Cargar y reproducir
                audio.load();
                await audio.play();
            } catch (error) {
                console.error('Error al reproducir la canción:', error);
            }
        },
        formatDuration(seconds) {
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = seconds % 60;
            return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
        }
    },

}
</script>
