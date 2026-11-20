<template>
    <AuthenticatedLayout>
        <Head title="Explorar" />

        <div class="ks-page">
            <section class="ks-hero mb-10 p-6 sm:p-8">
                <p class="ks-eyebrow">探索 · Catálogo</p>
                <h1 class="ks-title mt-2 text-3xl sm:text-4xl">Explorar KizunaSound</h1>
                <p class="ks-subtitle mt-2">Descubre música real de nuestro catálogo.</p>
            </section>

            <SectionHeading title="Géneros" subtitle="Elige un género para filtrar las canciones.">
                <div class="flex flex-wrap gap-3">
                    <Link :href="route('explore')"
                        class="rounded-full border px-4 py-2 text-sm transition"
                        :class="!activeGenre ? 'border-[var(--ks-accent)] bg-[var(--ks-accent)] text-warm' : 'border-warm/10 bg-warm/5 text-warm/75 hover:text-warm'">
                        Todos
                    </Link>
                    <Link v-for="genre in genres" :key="genre.id" :href="route('explore', { genre: genre.slug })"
                        class="rounded-full border px-4 py-2 text-sm transition"
                        :class="activeGenre?.id === genre.id ? 'border-[var(--ks-accent)] bg-[var(--ks-accent)] text-warm' : 'border-warm/10 bg-warm/5 text-warm/75 hover:text-warm'">
                        {{ genre.name }}
                    </Link>
                </div>
            </SectionHeading>

            <SectionHeading :title="activeGenre ? `Canciones de ${activeGenre.name}` : 'Canciones destacadas'">
                <TrackList v-if="songs.length" :songs="songs" />
                <EmptyState v-else title="No hay canciones en este género" description="Prueba con otro género o vuelve a 'Todos'." />
            </SectionHeading>

            <SectionHeading title="Artistas">
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                    <ArtistCard v-for="artist in artists" :key="artist.id" :artist="artist" />
                </div>
            </SectionHeading>

            <SectionHeading title="Álbumes">
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                    <AlbumCard v-for="album in albums" :key="album.id" :album="album" />
                </div>
                <EmptyState v-if="!albums.length" title="Aún no hay álbumes" />
            </SectionHeading>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import SectionHeading from '@/Components/Music/SectionHeading.vue'
import TrackList from '@/Components/Music/TrackList.vue'
import ArtistCard from '@/Components/Music/ArtistCard.vue'
import AlbumCard from '@/Components/Music/AlbumCard.vue'
import EmptyState from '@/Components/Music/EmptyState.vue'

defineProps({
    songs: { type: Array, default: () => [] },
    genres: { type: Array, default: () => [] },
    artists: { type: Array, default: () => [] },
    albums: { type: Array, default: () => [] },
    activeGenre: { type: Object, default: null },
})
</script>
