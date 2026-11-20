<template>
    <AuthenticatedLayout>
        <Head title="Tu biblioteca" />

        <div class="ks-page">
            <section class="ks-hero mb-10 flex flex-wrap items-end justify-between gap-4 p-6 sm:p-8">
                <div>
                    <p class="ks-eyebrow">図書館 · Espacio personal</p>
                    <h1 class="ks-title mt-2 text-3xl sm:text-4xl">Tu biblioteca</h1>
                    <p class="ks-subtitle mt-2">Todo lo que has guardado, en un solo lugar.</p>
                </div>
                <Link :href="route('playlists.create')" class="ks-btn ks-btn-primary">
                    Nueva playlist
                </Link>
            </section>

            <div class="mb-6 flex flex-wrap gap-2">
                <button v-for="tab in tabs" :key="tab.id" type="button" @click="active = tab.id"
                    class="rounded-full border px-4 py-2 text-sm transition"
                    :class="active === tab.id ? 'border-[var(--ks-accent)] bg-[var(--ks-accent)] text-warm' : 'border-warm/10 bg-warm/5 text-warm/75 hover:text-warm'">
                    {{ tab.label }} <span class="ml-1 text-xs opacity-70">{{ tab.count }}</span>
                </button>
            </div>

            <section v-show="active === 'favorites'">
                <TrackList v-if="favorites.length" :songs="favorites" />
                <EmptyState v-else title="Sin favoritos" description="Marca canciones como favoritas para verlas aquí." />
            </section>

            <section v-show="active === 'albums'">
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                    <AlbumCard v-for="album in albums" :key="album.id" :album="album" />
                </div>
                <EmptyState v-if="!albums.length" title="Sin álbumes guardados" />
            </section>

            <section v-show="active === 'artists'">
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                    <ArtistCard v-for="artist in artists" :key="artist.id" :artist="artist" />
                </div>
                <EmptyState v-if="!artists.length" title="No sigues a ningún artista" />
            </section>

            <section v-show="active === 'playlists'">
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                    <PlaylistCard v-for="playlist in playlists" :key="playlist.id" :playlist="playlist" />
                </div>
                <EmptyState v-if="!playlists.length" title="Aún no tienes playlists" description="Crea tu primera playlist." />
            </section>

            <section v-show="active === 'history'">
                <TrackList v-if="history.length" :songs="history" />
                <EmptyState v-else title="Sin historial" description="Escucha canciones para construir tu historial." />
            </section>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import TrackList from '@/Components/Music/TrackList.vue'
import AlbumCard from '@/Components/Music/AlbumCard.vue'
import ArtistCard from '@/Components/Music/ArtistCard.vue'
import PlaylistCard from '@/Components/Music/PlaylistCard.vue'
import EmptyState from '@/Components/Music/EmptyState.vue'

const props = defineProps({
    favorites: { type: Array, default: () => [] },
    albums: { type: Array, default: () => [] },
    artists: { type: Array, default: () => [] },
    playlists: { type: Array, default: () => [] },
    history: { type: Array, default: () => [] },
})

const active = ref('favorites')

const tabs = computed(() => [
    { id: 'favorites', label: 'Favoritos', count: props.favorites.length },
    { id: 'albums', label: 'Álbumes', count: props.albums.length },
    { id: 'artists', label: 'Artistas', count: props.artists.length },
    { id: 'playlists', label: 'Playlists', count: props.playlists.length },
    { id: 'history', label: 'Historial', count: props.history.length },
])
</script>
