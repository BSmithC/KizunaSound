<template>
    <AuthenticatedLayout>
        <Head title="Inicio" />

        <div class="ks-page">
            <section class="ks-hero mb-10 p-6 sm:p-10">
                <p class="ks-eyebrow">絆 · KizunaSound</p>
                <h1 class="mt-3 text-3xl font-black tracking-tight sm:text-5xl">
                    Hola, <span class="ks-gradient-text">{{ firstName }}</span>
                </h1>
                <p class="mt-3 max-w-xl text-sm leading-relaxed text-warm/55 sm:text-base">
                    Tu música, tu vínculo. Retoma donde lo dejaste o descubre algo nuevo seleccionado para ti.
                </p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <Link :href="route('explore')"
                        class="ks-btn ks-btn-primary">
                        Explorar catálogo
                    </Link>
                    <Link :href="route('library')"
                        class="ks-btn ks-btn-ghost">
                        Ir a mi biblioteca
                    </Link>
                </div>
            </section>

            <SectionHeading eyebrow="Para ti" title="Recomendadas" subtitle="Basadas en tus gustos y lo que has escuchado.">
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                    <SongCard v-for="song in recommendedSongs" :key="song.id" :song="song" :queue="recommendedSongs" />
                </div>
                <EmptyState v-if="!recommendedSongs.length" title="Aún no hay recomendaciones"
                    description="Explora el catálogo y guarda tus favoritos para personalizar esta sección." />
            </SectionHeading>

            <SectionHeading title="Escuchadas recientemente" subtitle="Retoma tu última sesión.">
                <TrackList v-if="recentSongs.length" :songs="recentSongs" />
                <EmptyState v-else title="Tu historial está vacío"
                    description="Las canciones que reproduzcas aparecerán aquí." />
            </SectionHeading>

            <SectionHeading title="Álbumes populares">
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                    <AlbumCard v-for="album in popularAlbums" :key="album.id" :album="album" />
                </div>
                <EmptyState v-if="!popularAlbums.length" title="Sin álbumes publicados"
                    description="Cuando el catálogo tenga álbumes, aparecerán aquí." />
            </SectionHeading>

            <SectionHeading title="Artistas destacados">
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                    <ArtistCard v-for="artist in featuredArtists" :key="artist.id" :artist="artist" />
                </div>
            </SectionHeading>

            <SectionHeading title="Nuevos lanzamientos">
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                    <SongCard v-for="song in newReleases" :key="song.id" :song="song" :queue="newReleases" />
                </div>
            </SectionHeading>

            <SectionHeading title="Playlists recomendadas">
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                    <PlaylistCard v-for="playlist in recommendedPlaylists" :key="playlist.id" :playlist="playlist" />
                </div>
                <EmptyState v-if="!recommendedPlaylists.length" title="Sin playlists públicas"
                    description="Crea la primera playlist o espera a que otras sean compartidas." />
            </SectionHeading>

            <SectionHeading title="Por estado de ánimo" subtitle="Selecciones reales agrupadas por mood.">
                <div class="flex flex-wrap gap-3">
                    <Link v-for="group in moods" :key="group.mood" :href="route('mood.index')"
                        class="rounded-full border border-warm/10 bg-warm/5 px-4 py-2 text-sm text-warm/85 transition hover:border-[var(--ks-accent)] hover:text-warm">
                        {{ group.mood }} <span class="ml-2 text-xs text-warm/45">{{ group.songs.length }}</span>
                    </Link>
                </div>
                <EmptyState v-if="!moods.length" title="Sin estados de ánimo asignados"
                    description="Asigna un mood a tus canciones para verlas agrupadas aquí." />
            </SectionHeading>

            <SectionHeading title="Música por género" subtitle="Filtra el catálogo por tu estilo favorito.">
                <div class="flex flex-wrap gap-3">
                    <Link v-for="genre in genres" :key="genre.id"
                        :href="route('explore', { genre: genre.slug })"
                        class="rounded-full border border-warm/10 bg-warm/5 px-4 py-2 text-sm text-warm/85 transition hover:border-[var(--ks-accent)] hover:text-warm">
                        {{ genre.name }} <span class="ml-2 text-xs text-warm/45">{{ genre.songs_count }}</span>
                    </Link>
                </div>
                <EmptyState v-if="!genres.length" title="No hay géneros activos" />
            </SectionHeading>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import SectionHeading from '@/Components/Music/SectionHeading.vue'
import SongCard from '@/Components/Music/SongCard.vue'
import AlbumCard from '@/Components/Music/AlbumCard.vue'
import ArtistCard from '@/Components/Music/ArtistCard.vue'
import PlaylistCard from '@/Components/Music/PlaylistCard.vue'
import TrackList from '@/Components/Music/TrackList.vue'
import EmptyState from '@/Components/Music/EmptyState.vue'

const props = defineProps({
    recommendedSongs: { type: Array, default: () => [] },
    recentSongs: { type: Array, default: () => [] },
    popularAlbums: { type: Array, default: () => [] },
    featuredArtists: { type: Array, default: () => [] },
    recommendedPlaylists: { type: Array, default: () => [] },
    newReleases: { type: Array, default: () => [] },
    genres: { type: Array, default: () => [] },
    moods: { type: Array, default: () => [] },
})

const page = usePage()
const firstName = computed(() => String(page.props.auth.user.name).split(' ')[0])
</script>
