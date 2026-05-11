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
            <div class="mx-auto max-w-7xl py-8 px-4 sm:px-6 lg:px-8">
                <div class="relative max-w-md mx-auto mb-12">
                    <MagnifyingGlassIcon
                        class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-800 dark:text-gray-200 transition-colors" />
                    <Input v-model="search"
                        class="w-full pl-12 pr-4 py-3 rounded-xl border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/50 backdrop-blur-sm focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-300"
                        placeholder="Search by song, artist, or album..." />
                </div>

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
                                        <button
                                            class="absolute bottom-4 right-4 p-3 bg-white rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 hover:scale-110">
                                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8 5v14l11-7z" />
                                            </svg>
                                        </button>
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
                                                <span class="text-xs text-gray-800 dark:text-gray-200">3:45</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Floating Action Button for Mobile -->
            <div class="fixed bottom-6 right-6 lg:hidden">
                <Link href="/songs/create"
                    class="flex items-center justify-center w-14 h-14 bg-gradient-to-r from-purple-600 to-pink-600 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-110">
                <AddIcon class="w-6 h-6 text-white" />
                </Link>
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
            searchQuery: this.filters?.search || '',
        }
    },
    watch: {
        searchQuery(newVal) {
            this.applyFilters(newVal)
        }
    },
    methods: {
        applyFilters(search) {
            router.get(
                '/songs',
                { search: search },
                {
                    preserveState: true,
                    replace: true,
                    preserveScroll: true,
                }
            )
        }
    }
}
</script>
