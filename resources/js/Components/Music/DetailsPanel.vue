<template>
    <Transition name="ks-slide">
        <aside
            v-if="details.isOpen"
            class="ks-scroll fixed inset-x-0 top-0 z-40 flex h-full flex-col border-l border-warm/10 bg-sumi/97 shadow-2xl shadow-black/20 dark:shadow-black/70 backdrop-blur-2xl sm:inset-x-auto sm:right-0 sm:h-[calc(100vh-4rem)] sm:max-w-md sm:top-16 lg:max-w-lg"
            aria-label="Panel de detalles"
        >
            <header class="flex items-center justify-between border-b border-warm/10 px-5 py-4">
                <div>
                    <p class="ks-eyebrow">{{ typeLabel }}</p>
                    <h2 class="mt-1 text-base font-semibold text-warm">Detalles</h2>
                </div>
                <button type="button" class="control-detail text-warm/60 hover:text-warm" aria-label="Cerrar panel de detalles" @click="details.close()">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M6 6l12 12M18 6 6 18" stroke-linecap="round" />
                    </svg>
                </button>
            </header>

            <div class="ks-scroll flex-1 overflow-y-auto px-5 py-6">
                <!-- Cola de reproducción -->
                <template v-if="details.type === 'queue'">
                    <p class="ks-subtitle mb-5">
                        {{ queue.length ? `${queue.length} canciones en la cola` : 'La cola está vacía. Reproduce una canción para comenzar.' }}
                    </p>

                    <ol v-if="queue.length" class="space-y-1">
                        <li v-for="(song, index) in queue" :key="song.id ?? index">
                            <button type="button" class="queue-row" @click="onQueueSong(song)">
                                <span class="w-6 flex-none text-right text-[11px] tabular-nums text-warm/40">
                                    <span v-if="isCurrent(song)" class="ks-playing-bars inline-flex"><span></span><span></span><span></span></span>
                                    <template v-else>{{ String(index + 1).padStart(2, '0') }}</template>
                                </span>
                                <MediaThumb :src="mediaUrl(song.thumbnail)" :alt="song.name" :label="initials(song.name)"
                                    rounded="rounded-md" class="h-9 w-9 flex-none" />
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate text-sm font-medium" :class="isCurrent(song) ? 'text-shu' : 'text-warm'">{{ song.name }}</span>
                                    <span class="block truncate text-xs text-warm/45">{{ song.artist?.name ?? 'Artista desconocido' }}</span>
                                </span>
                            </button>
                        </li>
                    </ol>

                    <div v-else class="mt-10 text-center">
                        <span class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full border border-warm/10 text-warm/40">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                                <path d="M4 6h16M4 12h10M4 18h7" stroke-linecap="round" />
                            </svg>
                        </span>
                        <p class="text-sm text-warm/55">Sin canciones en cola</p>
                    </div>
                </template>

                <!-- Canción -->
                <template v-else-if="details.type === 'song' && song">
                    <MediaThumb :src="mediaUrl(song.thumbnail)" :alt="song.name" :label="initials(song.name)"
                        rounded="rounded-2xl" class="aspect-square w-full shadow-ks-soft" />

                    <p class="ks-eyebrow mt-6">曲 · Canción</p>
                    <h3 class="mt-2 text-2xl font-bold leading-tight text-warm">{{ song.name }}</h3>
                    <p class="mt-2 text-sm text-warm/65">{{ song.artist?.name ?? 'Artista desconocido' }}</p>

                    <div class="mt-6 flex gap-2">
                        <button type="button" class="ks-btn ks-btn-primary flex-1" @click="playSong">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5.5v13l10-6.5L8 5.5Z" /></svg>
                            Reproducir
                        </button>
                        <Link v-if="song.id" :href="route('songs.show', song.id)" class="ks-btn ks-btn-ghost" title="Ver ficha completa">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M7 17 17 7M9 7h8v8" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </Link>
                    </div>

                    <dl class="mt-7 divide-y divide-warm/10 border-y border-warm/10">
                        <div v-if="song.album?.name" class="flex items-center justify-between gap-4 py-3">
                            <dt class="text-xs uppercase tracking-wider text-warm/40">Álbum</dt>
                            <dd class="truncate text-sm text-warm/85">{{ song.album.name }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-3">
                            <dt class="text-xs uppercase tracking-wider text-warm/40">Artista</dt>
                            <dd class="truncate text-sm text-warm/85">{{ song.artist?.name ?? 'Desconocido' }}</dd>
                        </div>
                        <div v-if="song.genre?.name" class="flex items-center justify-between gap-4 py-3">
                            <dt class="text-xs uppercase tracking-wider text-warm/40">Género</dt>
                            <dd class="truncate text-sm text-warm/85">{{ song.genre.name }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4 py-3">
                            <dt class="text-xs uppercase tracking-wider text-warm/40">Duración</dt>
                            <dd class="text-sm tabular-nums text-warm/85">{{ songDuration }}</dd>
                        </div>
                    </dl>
                </template>

                <!-- Álbum / Artista / Playlist -->
                <template v-else>
                    <MediaThumb
                        :src="coverSrc"
                        :alt="title"
                        :label="initials(title)"
                        :rounded="details.type === 'artist' ? 'rounded-full' : 'rounded-2xl'"
                        class="aspect-square w-full shadow-ks-soft"
                    />

                    <p class="ks-eyebrow mt-6">{{ japaneseLabel }}</p>
                    <h3 class="mt-2 text-2xl font-bold leading-tight text-warm">{{ title }}</h3>
                    <p class="mt-2 text-sm text-warm/65">{{ subtitle }}</p>

                    <p v-if="description" class="mt-5 text-sm leading-relaxed text-warm/60">{{ description }}</p>

                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <div v-for="stat in stats" :key="stat.label" class="rounded-xl border border-warm/10 bg-warm/5 px-4 py-3">
                            <p class="text-lg font-semibold tabular-nums text-warm">{{ stat.value }}</p>
                            <p class="mt-0.5 text-[11px] uppercase tracking-wider text-warm/40">{{ stat.label }}</p>
                        </div>
                    </div>

                    <Link v-if="detailRoute" :href="detailRoute" class="ks-btn ks-btn-ghost mt-6 w-full">
                        Ver página completa
                    </Link>
                </template>
            </div>

            <footer class="border-t border-warm/10 px-5 py-4">
                <p class="text-center text-[11px] uppercase tracking-[0.18em] text-warm/30">KizunaSound · 絆</p>
            </footer>
        </aside>
    </Transition>
</template>

<script setup>
import { computed, onMounted, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import { storeToRefs } from 'pinia'
import MediaThumb from '@/Components/Music/MediaThumb.vue'
import { initials, mediaUrl } from '@/helpers/media'
import { useDetailsStore } from '@/stores/detailsStore'
import { usePlayerStore } from '@/stores/playerStore'
import { usePlayback } from '@/composables/usePlayback'

const details = useDetailsStore()
const player = usePlayerStore()
const { playlist, currentSong } = storeToRefs(player)
const { play } = usePlayback()

const labels = {
    song: 'Canción · 曲',
    album: 'Álbum · アルバム',
    artist: 'Artista · アーティスト',
    playlist: 'Playlist · プレイリスト',
    queue: 'Cola · キュー',
}

const typeLabel = computed(() => labels[details.type] ?? 'Detalles')
const japaneseLabel = computed(() => labels[details.type] ?? 'Detalles')
const song = computed(() => (details.type === 'song' ? details.item : null))
const item = computed(() => (details.type !== 'song' ? details.item : null))
const queue = computed(() => playlist.value ?? [])

const title = computed(() => {
    if (song.value) return song.value.name ?? 'Canción'
    return item.value?.name ?? 'Sin selección'
})

const subtitle = computed(() => {
    if (details.type === 'album') return item.value?.artist?.name ?? 'Varios artistas'
    if (details.type === 'playlist') return item.value?.description || 'Playlist de KizunaSound'
    if (details.type === 'artist') return `${item.value?.songs_count ?? 0} canciones`
    return 'Selecciona un elemento para ver sus detalles'
})

const description = computed(() => {
    if (details.type === 'artist') return item.value?.biography || ''
    if (details.type === 'playlist') return item.value?.description || ''
    return ''
})

const coverSrc = computed(() => {
    if (!item.value) return null
    return mediaUrl(item.value.img ?? item.value.image ?? item.value.thumbnail)
})

const stats = computed(() => {
    const data = item.value ?? {}
    if (details.type === 'album') {
        return [
            { label: 'Canciones', value: data.songs_count ?? 0 },
            { label: 'Guardados', value: data.saved_by_count ?? 0 },
        ]
    }
    if (details.type === 'playlist') {
        return [
            { label: 'Canciones', value: data.songs_count ?? 0 },
            { label: 'Tipo', value: 'Lista' },
        ]
    }
    if (details.type === 'artist') {
        return [
            { label: 'Canciones', value: data.songs_count ?? 0 },
            { label: 'Álbumes', value: data.albums_count ?? 0 },
        ]
    }
    return []
})

const detailRoute = computed(() => {
    if (!item.value?.id) return null
    const routes = { album: 'albums.show', artist: 'artists.show', playlist: 'playlists.show' }
    const name = routes[details.type]
    return name ? route(name, item.value.id) : null
})

const songDuration = computed(() => {
    const seconds = Number(song.value?.duration_seconds ?? song.value?.duration ?? 0)
    if (!Number.isFinite(seconds) || seconds <= 0) return '—'
    const minutes = Math.floor(seconds / 60)
    const rest = String(Math.floor(seconds % 60)).padStart(2, '0')
    return `${minutes}:${rest}`
})

const isCurrent = (candidate) => currentSong.value?.id === candidate?.id

function onQueueSong(candidate) {
    player.playSong(candidate)
}

function playSong() {
    if (song.value) play(song.value, [song.value])
}

function onKeydown(event) {
    if (event.key === 'Escape' && details.isOpen) details.close()
}

onMounted(() => window.addEventListener('keydown', onKeydown))
onUnmounted(() => window.removeEventListener('keydown', onKeydown))
</script>

<style scoped>
.control-detail {
    display: inline-flex;
    height: 2.25rem;
    width: 2.25rem;
    align-items: center;
    justify-content: center;
    border-radius: 9999px;
    transition: color 150ms ease, background-color 150ms ease;
}

.control-detail:hover {
    background-color: rgb(var(--ks-warm-rgb) / 0.07);
}

.queue-row {
    display: flex;
    width: 100%;
    align-items: center;
    gap: 0.75rem;
    border-radius: 0.75rem;
    padding: 0.4rem 0.5rem;
    text-align: left;
    transition: background-color 150ms ease;
}

.queue-row:hover {
    background-color: rgb(var(--ks-warm-rgb) / 0.06);
}
</style>
