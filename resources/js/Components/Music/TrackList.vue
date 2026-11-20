<template>
    <div class="ks-card overflow-hidden">
        <div class="hidden grid-cols-[2rem_1fr_10rem_9rem] gap-3 border-b border-warm/10 px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.18em] text-warm/40 sm:grid">
            <span>#</span><span>Canción</span><span>Álbum / Género</span><span class="text-right">Duración</span>
        </div>

        <div v-for="(song, index) in songs" :key="song.id" role="button" tabindex="0"
            class="group grid w-full grid-cols-[2rem_1fr_auto] items-center gap-3 px-4 py-3 text-left transition-colors hover:bg-warm/5 focus:bg-warm/5 sm:grid-cols-[2rem_1fr_10rem_9rem]"
            @click="play(song, songs)" @keydown.enter.prevent="play(song, songs)" @keydown.space.prevent="play(song, songs)">
            <span class="flex items-center text-xs tabular-nums text-warm/40">
                <span v-if="isCurrent(song) && isPlaying" class="ks-playing-bars"><span></span><span></span><span></span></span>
                <template v-else>{{ String(index + 1).padStart(2, '0') }}</template>
            </span>
            <span class="flex min-w-0 items-center gap-3">
                <MediaThumb :src="mediaUrl(song.thumbnail)" :alt="song.name" :label="initials(song.name)"
                    rounded="rounded-md" class="h-10 w-10 flex-none ring-1 ring-warm/10" />
                <span class="min-w-0">
                    <span class="block truncate text-sm font-medium" :class="isCurrent(song) ? 'text-[var(--ks-accent)]' : 'text-warm'">
                        {{ song.name }}
                    </span>
                    <span class="block truncate text-xs text-warm/50">{{ song.artist?.name ?? 'Artista desconocido' }}</span>
                </span>
            </span>
            <span class="hidden truncate text-xs text-warm/50 sm:block">{{ song.genre?.name ?? '—' }}</span>
            <span class="flex items-center justify-end gap-2">
                <span class="text-xs tabular-nums text-warm/45">{{ formatDuration(song.duration_seconds) }}</span>
                <button type="button"
                    class="flex h-7 w-7 flex-none items-center justify-center rounded-full text-warm/35 opacity-0 transition hover:bg-warm/10 hover:text-warm group-hover:opacity-100 focus:opacity-100"
                    :aria-label="`Ver detalles de ${song.name}`" @click.stop="details.open('song', song)">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" /><path d="M12 11v5M12 8h.01" stroke-linecap="round" />
                    </svg>
                </button>
            </span>
        </div>

        <p v-if="!songs?.length" class="px-4 py-8 text-center text-sm text-warm/55">No hay canciones para mostrar.</p>
    </div>
</template>

<script setup>
import { storeToRefs } from 'pinia'
import MediaThumb from '@/Components/Music/MediaThumb.vue'
import { mediaUrl, initials, formatDuration } from '@/helpers/media'
import { usePlayback } from '@/composables/usePlayback'
import { useDetailsStore } from '@/stores/detailsStore'

defineProps({ songs: { type: Array, default: () => [] } })

const { player, play } = usePlayback()
const { currentSong, isPlaying } = storeToRefs(player)
const details = useDetailsStore()

const isCurrent = (song) => currentSong.value?.id === song?.id
</script>
