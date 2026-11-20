<template>
    <article class="ks-card group p-3">
        <MediaThumb :src="mediaUrl(playlist.img)" :alt="playlist.name" :label="initials(playlist.name)"
            class="aspect-square w-full">
            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-sumi/90 to-transparent p-3 pt-10">
                <span class="text-[11px] font-medium text-warm/75">{{ playlist.songs_count ?? 0 }} canciones</span>
            </div>
            <button type="button" @click.stop="details.open('playlist', playlist)"
                class="absolute right-3 top-3 flex h-8 w-8 items-center justify-center rounded-full border border-warm/15 bg-sumi/70 text-warm/70 opacity-0 backdrop-blur transition-opacity duration-150 group-hover:opacity-100 focus:opacity-100 hover:border-[var(--ks-accent)] hover:text-warm"
                :aria-label="`Ver detalles de ${playlist.name}`">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" /><path d="M12 11v5M12 8h.01" stroke-linecap="round" />
                </svg>
            </button>
        </MediaThumb>
        <div class="mt-3 min-w-0">
            <h3 class="truncate text-sm font-semibold text-warm">{{ playlist.name }}</h3>
            <p class="mt-1 truncate text-xs text-warm/55">{{ playlist.description || 'Playlist de KizunaSound' }}</p>
        </div>
    </article>
</template>

<script setup>
import MediaThumb from '@/Components/Music/MediaThumb.vue'
import { mediaUrl, initials } from '@/helpers/media'
import { useDetailsStore } from '@/stores/detailsStore'

defineProps({ playlist: { type: Object, required: true } })

const details = useDetailsStore()
</script>
