<template>
    <article class="ks-card group flex flex-col items-center p-5 text-center">
        <div class="relative">
            <MediaThumb :src="mediaUrl(artist.image)" :alt="artist.name" :label="initials(artist.name)"
                rounded="rounded-full" class="h-20 w-20 flex-none ring-1 ring-warm/10 transition-colors duration-150 group-hover:ring-[var(--ks-accent)]" />
            <button type="button" @click.stop="details.open('artist', artist)"
                class="absolute -bottom-1 -right-1 flex h-7 w-7 items-center justify-center rounded-full border border-warm/15 bg-sumi/85 text-warm/70 opacity-0 transition-opacity duration-150 group-hover:opacity-100 focus:opacity-100 hover:border-[var(--ks-accent)] hover:text-warm"
                :aria-label="`Ver detalles de ${artist.name}`">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" /><path d="M12 11v5M12 8h.01" stroke-linecap="round" />
                </svg>
            </button>
        </div>
        <h3 class="mt-4 w-full truncate text-sm font-semibold text-warm">{{ artist.name }}</h3>
        <p class="mt-1 text-xs text-warm/55">{{ artist.songs_count ?? 0 }} canciones</p>
    </article>
</template>

<script setup>
import MediaThumb from '@/Components/Music/MediaThumb.vue'
import { mediaUrl, initials } from '@/helpers/media'
import { useDetailsStore } from '@/stores/detailsStore'

defineProps({ artist: { type: Object, required: true } })

const details = useDetailsStore()
</script>
