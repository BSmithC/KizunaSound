<template>
    <article class="ks-card group p-3">
        <MediaThumb :src="mediaUrl(song.thumbnail)" :alt="song.name" :label="initials(song.name)"
            class="aspect-square w-full">
            <button type="button" @click="play(song, queue)"
                class="absolute bottom-3 right-3 flex h-10 w-10 items-center justify-center rounded-full bg-[var(--ks-accent)] text-warm opacity-0 shadow-ks-accent transition-opacity duration-150 group-hover:opacity-100 focus:opacity-100"
                :aria-label="`Reproducir ${song.name}`">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" /></svg>
            </button>
            <button type="button" @click.stop="details.open('song', song)"
                class="absolute left-3 top-3 flex h-8 w-8 items-center justify-center rounded-full border border-warm/15 bg-sumi/70 text-warm/70 opacity-0 backdrop-blur transition-opacity duration-150 group-hover:opacity-100 focus:opacity-100 hover:border-[var(--ks-accent)] hover:text-warm"
                :aria-label="`Ver detalles de ${song.name}`">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" /><path d="M12 11v5M12 8h.01" stroke-linecap="round" />
                </svg>
            </button>
            <span v-if="isCurrent && isPlaying"
                class="absolute right-3 top-3 flex h-7 items-center gap-1 rounded-full bg-sumi/80 px-2 backdrop-blur"
                aria-label="Reproduciendo ahora">
                <span class="ks-playing-bars"><span></span><span></span><span></span></span>
            </span>
        </MediaThumb>
        <div class="mt-3 min-w-0">
            <h3 class="truncate text-sm font-semibold text-warm">{{ song.name }}</h3>
            <p class="mt-1 truncate text-xs text-warm/55">{{ song.artist?.name ?? 'Artista desconocido' }}</p>
            <p v-if="song.genre?.name" class="mt-1 truncate text-[11px] font-medium text-[var(--ks-accent)]">{{ song.genre.name }}</p>
        </div>
    </article>
</template>

<script setup>
import { computed } from 'vue'
import { storeToRefs } from 'pinia'
import MediaThumb from '@/Components/Music/MediaThumb.vue'
import { mediaUrl, initials } from '@/helpers/media'
import { usePlayback } from '@/composables/usePlayback'
import { useDetailsStore } from '@/stores/detailsStore'

const props = defineProps({
    song: { type: Object, required: true },
    queue: { type: Array, default: () => [] },
})

const { player, play } = usePlayback()
const { currentSong, isPlaying } = storeToRefs(player)
const details = useDetailsStore()

const isCurrent = computed(() => currentSong.value?.id === props.song.id)
</script>
