<template>
    <div v-if="currentSong || error || loading" class="relative w-full">
        <div
            class="ks-surface-washi mx-auto max-w-[1600px] overflow-hidden rounded-2xl border border-warm/10 bg-sumi/95 shadow-2xl shadow-black/20 dark:shadow-black/70 backdrop-blur-xl"
        >
            <div
                v-if="error"
                class="flex items-center justify-between gap-3 border-b border-shu/25 bg-shu/10 px-3 py-2 text-xs text-warm sm:px-5"
                role="alert"
            >
                <span class="flex min-w-0 items-center gap-2">
                    <svg class="h-4 w-4 flex-none" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8" />
                        <path d="M12 7.5v5.5M12 16.2v.3" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    <span class="truncate">{{ error }}</span>
                </span>
                <button
                    type="button"
                    class="flex-none rounded-full bg-shu/30 px-3 py-1 font-semibold text-warm transition hover:bg-shu/50"
                    aria-label="Reintentar reproducción"
                    @click="onRetry"
                >
                    Reintentar
                </button>
            </div>

            <div
                v-else-if="warning"
                class="border-b border-washi/15 bg-washi/5 px-3 py-1.5 text-[11px] text-washi/85 sm:px-5"
                role="status"
            >
                {{ warning }}
            </div>

            <!-- Vista ampliada (solo capa visual, usa el mismo store) -->
            <Transition name="ks-expand">
                <div v-if="expanded && currentSong" class="border-b border-warm/10 px-4 py-5 sm:px-6">
                    <div class="flex items-start gap-4">
                        <MediaThumb
                            :src="coverSrc"
                            :alt="`Portada de ${songName}`"
                            :label="songInitials"
                            rounded="rounded-xl"
                            class="h-24 w-24 flex-none shadow-ks-soft ring-1 ring-warm/10 sm:h-32 sm:w-32"
                        />
                        <div class="min-w-0 flex-1">
                            <p class="ks-eyebrow">再生中 · En reproducción</p>
                            <h3 class="mt-2 truncate text-xl font-bold text-warm sm:text-2xl">{{ songName }}</h3>
                            <p class="mt-1 truncate text-sm text-warm/60">{{ artistName }} · {{ genreName }}</p>
                            <p v-if="nextTrackLabel" class="mt-2 truncate text-xs text-warm/40">{{ nextTrackLabel }}</p>
                        </div>
                        <button type="button" class="control-button flex-none text-warm/60 hover:text-warm" aria-label="Cerrar vista ampliada" @click="expanded = false">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M6 6l12 12M18 6 6 18" stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>

                    <div v-if="playlist?.length" class="mt-5">
                        <p class="mb-2 text-[10px] font-semibold uppercase tracking-[0.2em] text-warm/40">Cola · キュー</p>
                        <div class="ks-scroll flex gap-2 overflow-x-auto pb-1">
                            <button
                                v-for="(song, index) in playlist.slice(0, 8)"
                                :key="song.id ?? index"
                                type="button"
                                class="flex min-w-[11rem] flex-none items-center gap-2 rounded-xl border border-warm/10 bg-warm/5 px-2.5 py-2 text-left transition hover:border-[var(--ks-accent)] hover:bg-warm/10"
                                :aria-label="`Reproducir ${song.name}`"
                                @click="player.playSong(song)"
                            >
                                <span class="w-5 flex-none text-center text-[10px] tabular-nums text-warm/35">
                                    <span v-if="currentSong?.id === song.id" class="ks-playing-bars inline-flex"><span></span><span></span><span></span></span>
                                    <template v-else>{{ index + 1 }}</template>
                                </span>
                                <span class="min-w-0 flex-1 truncate text-xs" :class="currentSong?.id === song.id ? 'text-[var(--ks-accent)]' : 'text-warm/75'">
                                    {{ song.name }}
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>

            <div
                class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-3 px-3 py-2.5 sm:gap-5 sm:px-5 sm:py-3 md:grid-cols-[minmax(0,1fr)_minmax(0,1.4fr)_minmax(0,1fr)]"
            >
                <!-- Canción actual -->
                <div class="flex min-w-0 items-center gap-3">
                    <MediaThumb
                        :src="coverSrc"
                        :alt="`Portada de ${songName}`"
                        :label="songInitials"
                        rounded="rounded-lg"
                        class="h-11 w-11 flex-none ring-1 ring-warm/10 sm:h-14 sm:w-14"
                    />
                    <div class="min-w-0 flex-1">
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[var(--ks-accent)] sm:text-[11px]">
                            曲 · Reproduciendo
                        </p>
                        <h2 class="truncate text-sm font-semibold text-warm sm:text-base" :title="songName">
                            {{ songName }}
                        </h2>
                        <p class="truncate text-xs text-warm/55">
                            {{ artistName }} · {{ genreName }}
                        </p>
                        <p v-if="nextTrackLabel" class="mt-0.5 hidden truncate text-[11px] text-warm/40 md:block">
                            {{ nextTrackLabel }}
                        </p>
                    </div>
                    <button
                        v-if="currentSong"
                        type="button"
                        class="control-button hidden flex-none text-warm/60 hover:text-warm lg:inline-flex"
                        aria-label="Ver detalles de la canción actual"
                        @click="details.open('song', currentSong)"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" /><path d="M12 11v5M12 8h.01" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>

                <!-- Controles y progreso -->
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center justify-center gap-1 sm:gap-2">
                        <button
                            type="button"
                            class="control-button hidden sm:inline-flex"
                            :class="isFavorite ? 'text-[var(--ks-accent)]' : 'text-warm/55 hover:text-warm'"
                            :aria-label="isFavorite ? 'Quitar de favoritos' : 'Añadir a favoritos'"
                            :aria-pressed="isFavorite ? 'true' : 'false'"
                            @click="onToggleFavorite"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path
                                    d="M12 20.3 4.6 13a4.7 4.7 0 0 1 6.6-6.7l.8.8.8-.8A4.7 4.7 0 1 1 19.4 13L12 20.3Z"
                                    :fill="isFavorite ? 'currentColor' : 'none'"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>
                        </button>

                        <button
                            type="button"
                            class="control-button hidden sm:inline-flex"
                            :class="shuffle ? 'text-[var(--ks-accent)]' : 'text-warm/55 hover:text-warm'"
                            :aria-label="shuffle ? 'Desactivar reproducción aleatoria' : 'Activar reproducción aleatoria'"
                            :aria-pressed="shuffle ? 'true' : 'false'"
                            @click="onToggleShuffle"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path
                                    d="M16 3h5v5M4 20 21 3M21 16v5h-5M15 15l6 6M4 4l5 5"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>
                        </button>

                        <button
                            type="button"
                            class="control-button text-warm/60 hover:text-warm"
                            aria-label="Canción anterior"
                            :disabled="!hasPrevious"
                            @click="onPrevious"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M18 5v14l-9-7 9-7Z" fill="currentColor" />
                                <path d="M6 5v14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </button>

                        <button
                            type="button"
                            class="rounded-full border border-warm/10 px-2 py-1 text-[11px] font-bold tabular-nums text-warm/75 transition hover:border-[var(--ks-accent)] hover:text-warm md:hidden"
                            :aria-label="`Velocidad de reproducción: ${playbackSpeed}x. Cambiar velocidad`"
                            @click="onTogglePlaybackSpeed"
                        >
                            {{ playbackSpeed }}x
                        </button>

                        <button type="button" class="control-button hidden text-warm/60 hover:text-warm sm:inline-flex" aria-label="Retroceder 10 segundos" @click="onRewind">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path
                                    d="M6.5 16.9a7 7 0 1 0-.4-9.4M5 5v3.1c0 .5.4.9.9.9H9"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <text x="12" y="15.5" text-anchor="middle" font-size="7" fill="currentColor">10</text>
                            </svg>
                        </button>

                        <button
                            type="button"
                            class="relative flex h-11 w-11 flex-none items-center justify-center rounded-full bg-[var(--ks-accent)] text-warm shadow-ks-accent transition-colors duration-150 hover:brightness-110 focus:outline-none focus-visible:ring-2 focus-visible:ring-[var(--ks-accent)] focus-visible:ring-offset-2 focus-visible:ring-offset-sumi sm:h-12 sm:w-12"
                            :aria-label="isPlaying ? 'Pausar' : 'Reproducir'"
                            @click="onTogglePlay"
                        >
                            <svg v-if="isPlaying" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <rect x="6" y="4" width="4" height="16" rx="1" />
                                <rect x="14" y="4" width="4" height="16" rx="1" />
                            </svg>
                            <svg v-else class="ml-0.5 h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M8 5.5v13l10-6.5L8 5.5Z" />
                            </svg>
                            <span
                                v-if="loading"
                                class="absolute inset-0 flex items-center justify-center rounded-full bg-sumi/55"
                                aria-hidden="true"
                            >
                                <svg class="h-5 w-5 animate-spin text-warm" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-opacity="0.25" stroke-width="3" />
                                    <path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round" />
                                </svg>
                            </span>
                        </button>

                        <button type="button" class="control-button hidden text-warm/60 hover:text-warm sm:inline-flex" aria-label="Adelantar 10 segundos" @click="onForward">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path
                                    d="M17.5 16.9a7 7 0 1 1 .4-9.4M19 5v3.1c0 .5-.4.9-.9.9H15"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <text x="12" y="15.5" text-anchor="middle" font-size="7" fill="currentColor">10</text>
                            </svg>
                        </button>

                        <button
                            type="button"
                            class="control-button text-warm/60 hover:text-warm"
                            aria-label="Siguiente canción"
                            :disabled="!hasNext"
                            @click="onNext"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M6 5v14l9-7L6 5Z" fill="currentColor" />
                                <path d="M18 5v14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </button>

                        <button
                            type="button"
                            class="control-button relative hidden sm:inline-flex"
                            :class="repeat !== 'off' ? 'text-[var(--ks-accent)]' : 'text-warm/55 hover:text-warm'"
                            :aria-label="repeatLabel"
                            :aria-pressed="repeat !== 'off' ? 'true' : 'false'"
                            @click="onToggleRepeat"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path
                                    d="M17 2l4 4-4 4M3 11V9a3 3 0 0 1 3-3h15M7 22l-4-4 4-4M21 13v2a3 3 0 0 1-3 3H3"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>
                            <span
                                v-if="repeat === 'one'"
                                class="absolute -bottom-1 -right-1 rounded-full bg-[var(--ks-accent)] px-1 text-[8px] font-bold leading-4 text-warm"
                                aria-hidden="true"
                            >
                                1
                            </span>
                        </button>
                    </div>

                    <div class="mt-2 flex items-center gap-2">
                        <span class="w-9 text-right text-[10px] tabular-nums text-warm/55 sm:text-xs">
                            {{ formattedCurrentTime }}
                        </span>

                        <div
                            class="group relative h-4 flex-1 cursor-pointer focus:outline-none"
                            role="progressbar"
                            tabindex="0"
                            :aria-label="`Progreso de ${songName}`"
                            :aria-valuenow="progressAriaNow"
                            aria-valuemin="0"
                            :aria-valuemax="progressAriaMax"
                            @click="onSeek"
                            @keydown.left.prevent="onRewind"
                            @keydown.right.prevent="onForward"
                        >
                            <div class="absolute inset-x-0 top-1/2 h-1.5 -translate-y-1/2 rounded-full bg-warm/10">
                                <div
                                    class="h-full rounded-full bg-[var(--ks-accent)]"
                                    :style="{ width: `${progressPercentage}%` }"
                                ></div>
                            </div>
                            <div
                                class="absolute top-1/2 h-3 w-3 -translate-x-1/2 -translate-y-1/2 rounded-full bg-warm opacity-0 shadow ring-2 ring-[var(--ks-accent)] transition-opacity group-hover:opacity-100 group-focus:opacity-100"
                                :style="{ left: `${progressPercentage}%` }"
                                aria-hidden="true"
                            ></div>
                        </div>

                        <span class="w-9 text-[10px] tabular-nums text-warm/45 sm:text-xs">
                            {{ formattedDuration }}
                        </span>
                    </div>

                    <p v-if="nextTrackLabel" class="mt-1 truncate text-center text-[11px] text-warm/40 md:hidden">
                        {{ nextTrackLabel }}
                    </p>
                </div>

                <!-- Velocidad, cola, ampliar y volumen -->
                <div class="hidden items-center justify-center gap-2 md:flex md:justify-end">
                    <button
                        type="button"
                        class="control-button relative text-warm/60 hover:text-warm"
                        aria-label="Ver cola de reproducción"
                        @click="details.openQueue()"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M4 6h12M4 12h12M4 18h8M18 15l3 3-3 3" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>

                    <button
                        type="button"
                        class="control-button text-warm/60 hover:text-warm"
                        :aria-label="expanded ? 'Cerrar reproductor ampliado' : 'Ampliar reproductor'"
                        :aria-pressed="expanded ? 'true' : 'false'"
                        @click="expanded = !expanded"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path v-if="expanded" d="M9 15 15 9M9 9h6v6" stroke-linecap="round" stroke-linejoin="round" />
                            <path v-else d="M15 9l6-6M15 9h6V3M9 15l-6 6M9 15H3v6" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>

                    <button
                        type="button"
                        class="rounded-full border border-warm/10 px-2.5 py-1 text-[11px] font-bold tabular-nums text-warm/75 transition hover:border-[var(--ks-accent)] hover:text-warm"
                        :aria-label="`Velocidad de reproducción: ${playbackSpeed}x. Cambiar velocidad`"
                        @click="onTogglePlaybackSpeed"
                    >
                        {{ playbackSpeed }}x
                    </button>

                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            class="control-button text-warm/60 hover:text-warm"
                            :aria-label="isMuted ? 'Activar sonido' : 'Silenciar'"
                            :aria-pressed="isMuted ? 'true' : 'false'"
                            @click="onToggleMute"
                        >
                            <svg v-if="isMuted" class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path
                                    d="M11 5 6.5 9H3v6h3.5L11 19V5ZM16 9l5 6M21 9l-5 6"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>
                            <svg v-else class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path
                                    d="M11 5 6.5 9H3v6h3.5L11 19V5Z"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M15 9a4 4 0 0 1 0 6M18 6a8 8 0 0 1 0 12"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                />
                            </svg>
                        </button>

                        <input
                            type="range"
                            min="0"
                            max="1"
                            step="0.05"
                            :value="volume"
                            class="ks-range h-1.5 w-16 cursor-pointer sm:w-24 lg:w-32"
                            aria-label="Volumen"
                            @input="onVolumeInput"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { storeToRefs } from 'pinia'
import MediaThumb from '@/Components/Music/MediaThumb.vue'
import { initials, mediaUrl } from '@/helpers/media'
import { usePlayerStore } from '@/stores/playerStore'
import { useDetailsStore } from '@/stores/detailsStore'

const player = usePlayerStore()
const details = useDetailsStore()
const expanded = ref(false)

const {
    currentSong,
    isPlaying,
    currentTime,
    duration,
    progressPercentage,
    isFavorite,
    volume,
    isMuted,
    playbackSpeed,
    playlist,
    currentIndex,
    shuffle,
    repeat,
    hasNext,
    hasPrevious,
    loading,
    error,
    warning,
    formattedCurrentTime,
    formattedDuration,
} = storeToRefs(player)

const songName = computed(() => currentSong.value?.name || 'Canción desconocida')
const artistName = computed(() => currentSong.value?.artist?.name || 'Artista desconocido')
const genreName = computed(() => currentSong.value?.genre?.name || 'Género desconocido')
const coverSrc = computed(() => mediaUrl(currentSong.value?.thumbnail))
const songInitials = computed(() => initials(songName.value))
const progressAriaNow = computed(() => Math.floor(currentTime.value || 0))
const progressAriaMax = computed(() => Math.ceil(duration.value || 0))

const repeatLabel = computed(() => {
    if (repeat.value === 'one') return 'Repetir canción actual (activado)'
    if (repeat.value === 'all') return 'Repetir lista de reproducción (activado)'
    return 'Repetición desactivada'
})

const nextTrackLabel = computed(() => {
    if (!playlist.value?.length) return ''
    if (repeat.value === 'one') return `Siguiente: ${songName.value} (se repetirá)`
    if (shuffle.value) return 'Siguiente: canción aleatoria'

    const index = currentIndex.value + 1
    const nextSong = playlist.value[index] ?? (repeat.value === 'all' ? playlist.value[0] : null)
    return nextSong ? `Siguiente: ${nextSong.name}` : ''
})

const onTogglePlay = () => player.togglePlay()
const onPrevious = () => player.previous()
const onNext = () => player.next()
const onRewind = () => player.rewind()
const onForward = () => player.forward()
const onSeek = (event) => player.seek(event)
const onToggleShuffle = () => { shuffle.value = !shuffle.value }
const onToggleRepeat = () => {
    const order = ['off', 'all', 'one']
    repeat.value = order[(order.indexOf(repeat.value) + 1) % order.length]
}
const onTogglePlaybackSpeed = () => player.togglePlaybackSpeed()
const onToggleMute = () => player.toggleMute()
const onVolumeInput = (event) => player.setVolume(event.target.valueAsNumber)
const onToggleFavorite = () => player.toggleFavorite()
const onRetry = () => player.retry()
</script>

<style scoped>
.control-button {
    display: inline-flex;
    height: 2.25rem;
    width: 2.25rem;
    flex: none;
    align-items: center;
    justify-content: center;
    border-radius: 9999px;
    transition: color 150ms ease, background-color 150ms ease;
}

.control-button:hover:not(:disabled) {
    background-color: rgb(var(--ks-warm-rgb) / 0.07);
}

.control-button:disabled {
    cursor: not-allowed;
    opacity: 0.35;
}

.control-button:focus-visible {
    outline: 2px solid var(--ks-accent);
    outline-offset: 2px;
}
</style>
