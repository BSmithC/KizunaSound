<template>
    <AuthenticatedLayout>
        <Head :title="playlist.name" />

        <div class="ks-page">
            <div class="mb-6">
                <Link :href="route('playlists.index')" class="text-sm text-warm/55 hover:text-warm">← Volver a playlists</Link>
            </div>

            <section class="ks-hero mb-8 flex flex-col gap-6 p-6 sm:flex-row sm:items-center">
                <MediaThumb :src="mediaUrl(playlist.img)" :alt="playlist.name" :label="initials(playlist.name)"
                    class="h-48 w-full flex-none sm:h-40 sm:w-40" />
                <div class="min-w-0 flex-1">
                    <p class="ks-eyebrow">Playlist</p>
                    <h1 class="mt-2 text-3xl font-black tracking-tight text-warm sm:text-4xl">{{ playlist.name }}</h1>
                    <p class="mt-2 text-sm text-warm/55">{{ playlist.description || 'Sin descripción.' }}</p>
                    <p class="mt-1 text-xs text-warm/45">Por {{ playlist.owner?.name ?? 'Usuario desconocido' }}</p>
                    <div class="mt-4 flex flex-wrap gap-2 text-xs text-warm/55">
                        <span class="rounded-full border border-warm/10 bg-warm/5 px-3 py-1">{{ playlist.songs_count ?? playlist.songs?.length ?? 0 }} canciones</span>
                        <span class="rounded-full border px-3 py-1"
                            :class="playlist.is_public ? 'border-emerald-500/30 text-emerald-300' : 'border-amber-500/30 text-amber-300'">
                            {{ playlist.is_public ? 'Pública' : 'Privada' }}
                        </span>
                        <span v-if="playlist.is_collaborative" class="rounded-full border border-cyan-500/30 px-3 py-1 text-cyan-300">Colaborativa</span>
                    </div>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <button v-if="playlist.songs?.length" type="button"
                            class="flex items-center gap-2 ks-btn ks-btn-primary"
                            @click="playAll">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" /></svg>
                            Reproducir playlist
                        </button>
                        <Link v-if="canManage" :href="route('playlists.edit', playlist.id)"
                            class="ks-btn ks-btn-ghost">Editar</Link>
                    </div>
                </div>
            </section>

            <SectionHeading title="Canciones" subtitle="Usa el reproductor global para escuchar la lista.">
                <TrackList v-if="playlist.songs?.length" :songs="playlist.songs" />
                <EmptyState v-else title="Playlist vacía" description="Edita la playlist para añadir canciones." />
            </SectionHeading>

            <div v-if="canManage && playlist.songs?.length" class="ks-card mt-6 p-6">
                <h2 class="mb-4 text-lg font-semibold text-warm">Gestionar pistas</h2>
                <div class="space-y-2">
                    <div v-for="song in playlist.songs" :key="song.id" class="flex items-center gap-3 rounded-lg border border-warm/10 px-3 py-2">
                        <MediaThumb :src="mediaUrl(song.thumbnail)" :alt="song.name" :label="initials(song.name)"
                            rounded="rounded-md" class="h-9 w-9 flex-none" />
                        <span class="min-w-0 flex-1 truncate text-sm text-warm/85">{{ song.name }}</span>
                        <button type="button" class="text-xs text-rose-400 hover:text-rose-300" @click="removeSong(song)">Quitar</button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import MediaThumb from '@/Components/Music/MediaThumb.vue'
import SectionHeading from '@/Components/Music/SectionHeading.vue'
import TrackList from '@/Components/Music/TrackList.vue'
import EmptyState from '@/Components/Music/EmptyState.vue'
import { mediaUrl, initials } from '@/helpers/media'
import { usePlayback } from '@/composables/usePlayback'
import { togglePlaylistSong } from '@/helpers/libraryApi'

const props = defineProps({
    playlist: { type: Object, required: true },
    canManage: { type: Boolean, default: false },
})

const { play } = usePlayback()

const playAll = async () => {
    const songs = props.playlist.songs ?? []
    if (! songs.length) return
    await play(songs[0], songs)
}

const removeSong = async (song) => {
    try {
        await togglePlaylistSong(props.playlist.id, song.id)
        router.reload({ only: ['playlist'] })
    } catch (error) {
        console.error('No se pudo quitar la canción', error)
    }
}
</script>
