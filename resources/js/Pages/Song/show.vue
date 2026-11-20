<template>
    <AuthenticatedLayout>
        <Head :title="song.name" />

        <div class="ks-page">
            <div class="mb-6">
                <Link :href="route('songs.index')" class="text-sm text-warm/55 hover:text-warm">← Volver a canciones</Link>
            </div>

            <section class="ks-hero mb-8 flex flex-col gap-6 p-6 sm:flex-row sm:items-center">
                <MediaThumb :src="mediaUrl(song.thumbnail)" :alt="song.name" :label="initials(song.name)"
                    class="h-44 w-full flex-none sm:h-40 sm:w-40" />
                <div class="min-w-0 flex-1">
                    <p class="ks-eyebrow">Canción</p>
                    <h1 class="mt-2 text-3xl font-black tracking-tight text-warm sm:text-4xl">{{ song.name }}</h1>
                    <p class="mt-2 text-sm text-warm/75">
                        {{ song.artist?.name ?? 'Artista desconocido' }}
                        <span v-if="song.genre?.name" class="mx-2 text-warm/35">•</span>
                        <span v-if="song.genre?.name">{{ song.genre.name }}</span>
                    </p>
                    <div class="mt-4 flex flex-wrap gap-2 text-xs text-warm/55">
                        <span v-if="song.mood" class="rounded-full border border-warm/10 bg-warm/5 px-3 py-1">Mood: {{ song.mood }}</span>
                        <span class="rounded-full border border-warm/10 bg-warm/5 px-3 py-1">{{ formatDuration(song.duration_seconds) }}</span>
                        <span class="rounded-full border px-3 py-1"
                            :class="song.is_public ? 'border-emerald-500/30 text-emerald-300' : 'border-amber-500/30 text-amber-300'">
                            {{ song.is_public ? 'Pública' : 'Privada' }}
                        </span>
                    </div>

                    <div class="mt-6 flex flex-wrap items-center gap-3">
                        <button type="button"
                            class="flex items-center gap-2 ks-btn ks-btn-primary"
                            @click="playThis">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" /></svg>
                            Reproducir
                        </button>
                        <button type="button"
                            class="rounded-full border border-warm/15 px-5 py-2.5 text-sm text-warm/75 transition hover:text-warm"
                            @click="favorite">
                            {{ isFavorite ? '♥ En favoritos' : '♡ Añadir a favoritos' }}
                        </button>
                        <Link v-if="canManage" :href="route('songs.edit', song.id)"
                            class="ks-btn ks-btn-ghost">
                            Editar
                        </Link>
                    </div>
                </div>
            </section>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="ks-card p-6 lg:col-span-2">
                    <h2 class="text-lg font-semibold text-warm">Letra</h2>
                    <pre v-if="song.lyrics" class="mt-4 whitespace-pre-wrap font-sans text-sm leading-7 text-warm/75">{{ song.lyrics }}</pre>
                    <EmptyState v-else title="Sin letra" description="Esta canción aún no tiene letra registrada." />
                </div>

                <div class="ks-card p-6">
                    <h2 class="text-lg font-semibold text-warm">Ficha técnica</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex justify-between gap-4">
                            <dt class="text-warm/45">Subida por</dt>
                            <dd class="text-right text-warm/85">{{ song.uploader?.name ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-warm/45">Licencia</dt>
                            <dd class="text-right text-warm/85">{{ song.license ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-warm/45">Lanzamiento</dt>
                            <dd class="text-right text-warm/85">{{ song.release_date ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-warm/45">Reproducciones</dt>
                            <dd class="text-right tabular-nums text-warm/85">{{ song.play_count ?? 0 }}</dd>
                        </div>
                    </dl>

                    <div v-if="song.albums?.length" class="mt-6 border-t border-warm/10 pt-5">
                        <h3 class="mb-3 text-sm font-semibold text-warm">Álbumes</h3>
                        <div class="space-y-2">
                            <Link v-for="album in song.albums" :key="album.id" :href="route('albums.show', album.id)"
                                class="flex items-center gap-3 rounded-lg p-2 transition hover:bg-warm/5">
                                <MediaThumb :src="mediaUrl(album.img)" :alt="album.name" :label="initials(album.name)"
                                    rounded="rounded-md" class="h-10 w-10 flex-none" />
                                <span class="truncate text-sm text-warm/75">{{ album.name }}</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import MediaThumb from '@/Components/Music/MediaThumb.vue'
import EmptyState from '@/Components/Music/EmptyState.vue'
import { mediaUrl, initials, formatDuration } from '@/helpers/media'
import { usePlayback } from '@/composables/usePlayback'
import { toggleFavorite } from '@/helpers/libraryApi'

const props = defineProps({
    song: { type: Object, required: true },
    canManage: { type: Boolean, default: false },
    isFavorite: { type: Boolean, default: false },
})

const { play } = usePlayback()
const isFavorite = ref(props.isFavorite)

const playThis = async () => {
    await play(props.song, [props.song])
}

const favorite = async () => {
    try {
        const result = await toggleFavorite(props.song.id)
        isFavorite.value = Boolean(result.favorite)
    } catch (error) {
        console.error('No se pudo actualizar favoritos', error)
    }
}
</script>
