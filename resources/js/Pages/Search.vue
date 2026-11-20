<template>
    <AuthenticatedLayout>
        <Head title="Buscar" />

        <div class="ks-page">
            <section class="ks-hero mb-10 p-6 sm:p-8">
                <p class="ks-eyebrow">検索 · Buscador</p>
                <h1 class="ks-title mt-2 text-3xl sm:text-4xl">Encuentra tu música</h1>

                <div class="relative mt-5 max-w-2xl">
                    <input v-model="term" type="search" placeholder="Canciones, artistas, álbumes, playlists, géneros..."
                        class="h-12 w-full rounded-full border border-warm/10 bg-warm/5 px-5 pr-12 text-sm text-warm placeholder-slate-500 outline-none transition focus:border-[var(--ks-accent)]"
                        @input="debouncedSearch" @keyup.enter="commit" />
                    <span v-if="loading" class="absolute inset-y-0 right-5 flex items-center text-[var(--ks-accent)]">
                        <span class="h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent" />
                    </span>
                </div>

                <div v-if="history.length" class="mt-4 flex flex-wrap items-center gap-2">
                    <span class="text-xs text-warm/45">Recientes:</span>
                    <button v-for="item in history" :key="item" type="button"
                        class="rounded-full border border-warm/10 bg-warm/5 px-3 py-1 text-xs text-warm/75 hover:border-[var(--ks-accent)] hover:text-warm"
                        @click="searchFor(item)">
                        {{ item }}
                    </button>
                    <button type="button" class="text-xs text-warm/35 underline hover:text-warm/75" @click="clearHistory">
                        Limpiar
                    </button>
                </div>
            </section>

            <template v-if="hasResults">
                <p class="mb-6 text-sm text-warm/55">
                    Resultados para <span class="font-semibold text-warm">"{{ query }}"</span>
                </p>

                <SectionHeading title="Canciones">
                    <TrackList :songs="songs" />
                </SectionHeading>

                <SectionHeading title="Artistas">
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                        <ArtistCard v-for="artist in artists" :key="artist.id" :artist="artist" />
                    </div>
                    <EmptyState v-if="!artists.length" title="Sin artistas" />
                </SectionHeading>

                <SectionHeading title="Álbumes">
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                        <AlbumCard v-for="album in albums" :key="album.id" :album="album" />
                    </div>
                    <EmptyState v-if="!albums.length" title="Sin álbumes" />
                </SectionHeading>

                <SectionHeading title="Playlists">
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                        <PlaylistCard v-for="playlist in playlists" :key="playlist.id" :playlist="playlist" />
                    </div>
                    <EmptyState v-if="!playlists.length" title="Sin playlists" />
                </SectionHeading>

                <SectionHeading title="Géneros">
                    <div class="flex flex-wrap gap-3">
                        <Link v-for="genre in genres" :key="genre.id" :href="route('explore', { genre: genre.slug })"
                            class="rounded-full border border-warm/10 bg-warm/5 px-4 py-2 text-sm text-warm/85 hover:border-[var(--ks-accent)]">
                            {{ genre.name }}
                        </Link>
                    </div>
                </SectionHeading>
            </template>

            <EmptyState v-else-if="query.length >= 2" title="Sin resultados"
                :description="`No encontramos coincidencias para '${query}'. Prueba con otro término.`" />

            <EmptyState v-else title="¿Qué quieres escuchar?" description="Escribe al menos dos caracteres para buscar." />
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { router, Head, Link } from '@inertiajs/vue3'
import { useDebounceFn } from '@vueuse/core'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import SectionHeading from '@/Components/Music/SectionHeading.vue'
import TrackList from '@/Components/Music/TrackList.vue'
import ArtistCard from '@/Components/Music/ArtistCard.vue'
import AlbumCard from '@/Components/Music/AlbumCard.vue'
import PlaylistCard from '@/Components/Music/PlaylistCard.vue'
import EmptyState from '@/Components/Music/EmptyState.vue'
import { getSearchHistory, pushSearchHistory, clearSearchHistory } from '@/helpers/searchHistory'

const props = defineProps({
    query: { type: String, default: '' },
    songs: { type: Array, default: () => [] },
    artists: { type: Array, default: () => [] },
    albums: { type: Array, default: () => [] },
    playlists: { type: Array, default: () => [] },
    genres: { type: Array, default: () => [] },
})

const term = ref(props.query)
const loading = ref(false)
const history = ref(getSearchHistory())

watch(() => props.query, (value) => { term.value = value })

const hasResults = computed(() =>
    props.songs.length || props.artists.length || props.albums.length || props.playlists.length || props.genres.length
)

const run = () => {
    loading.value = true
    router.get(route('search'), { q: term.value }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onFinish: () => { loading.value = false },
    })
}

const debouncedSearch = useDebounceFn(run, 350)

const commit = () => {
    history.value = pushSearchHistory(term.value)
    loading.value = true
    router.get(route('search'), { q: term.value }, {
        preserveScroll: true,
        onFinish: () => { loading.value = false },
    })
}

const searchFor = (value) => {
    term.value = value
    commit()
}

const clearHistory = () => { history.value = clearSearchHistory() }
</script>
