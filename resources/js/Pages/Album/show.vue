<template>
    <AuthenticatedLayout>
        <Head :title="album.name" />

        <div class="ks-page">
            <div class="mb-6">
                <Link :href="route('albums.index')" class="text-sm text-warm/55 hover:text-warm">← Volver a álbumes</Link>
            </div>

            <section class="ks-hero mb-8 flex flex-col gap-6 p-6 sm:flex-row sm:items-center">
                <MediaThumb :src="mediaUrl(album.img)" :alt="album.name" :label="initials(album.name)"
                    class="h-48 w-full flex-none sm:h-40 sm:w-40" />
                <div class="min-w-0 flex-1">
                    <p class="ks-eyebrow">Álbum</p>
                    <h1 class="mt-2 text-3xl font-black tracking-tight text-warm sm:text-4xl">{{ album.name }}</h1>
                    <p class="mt-2 text-sm text-warm/75">
                        {{ album.artist?.name ?? album.owner?.name ?? 'Varios artistas' }}
                    </p>
                    <div class="mt-4 flex flex-wrap gap-2 text-xs text-warm/55">
                        <span class="rounded-full border border-warm/10 bg-warm/5 px-3 py-1">{{ album.songs_count ?? album.songs?.length ?? 0 }} pistas</span>
                        <span v-if="album.release_date" class="rounded-full border border-warm/10 bg-warm/5 px-3 py-1">{{ album.release_date }}</span>
                        <span v-if="album.license" class="rounded-full border border-warm/10 bg-warm/5 px-3 py-1">{{ album.license }}</span>
                        <span class="rounded-full border px-3 py-1"
                            :class="album.is_public ? 'border-emerald-500/30 text-emerald-300' : 'border-amber-500/30 text-amber-300'">
                            {{ album.is_public ? 'Público' : 'Privado' }}
                        </span>
                    </div>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <button v-if="album.songs?.length" type="button"
                            class="flex items-center gap-2 ks-btn ks-btn-primary"
                            @click="playAll">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" /></svg>
                            Reproducir álbum
                        </button>
                        <button type="button" class="ks-btn ks-btn-ghost"
                            @click="saveAlbum">
                            {{ saved ? '♥ Guardado' : '♡ Guardar álbum' }}
                        </button>
                        <Link v-if="canManage" :href="route('albums.edit', album.id)"
                            class="ks-btn ks-btn-ghost">Editar</Link>
                        <button v-if="canManage" type="button"
                            class="rounded-full border border-rose-500/40 px-5 py-2.5 text-sm text-rose-300 hover:bg-rose-500/10"
                            @click="remove">Eliminar</button>
                    </div>
                </div>
            </section>

            <SectionHeading title="Pistas" subtitle="Reproduce el álbum completo o elige una canción.">
                <TrackList v-if="album.songs?.length" :songs="album.songs" />
                <EmptyState v-else title="Álbum sin canciones" description="Edita el álbum para añadir pistas." />
            </SectionHeading>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import MediaThumb from '@/Components/Music/MediaThumb.vue'
import SectionHeading from '@/Components/Music/SectionHeading.vue'
import TrackList from '@/Components/Music/TrackList.vue'
import EmptyState from '@/Components/Music/EmptyState.vue'
import { mediaUrl, initials } from '@/helpers/media'
import { usePlayback } from '@/composables/usePlayback'
import { toggleAlbum } from '@/helpers/libraryApi'

const props = defineProps({
    album: { type: Object, required: true },
    canManage: { type: Boolean, default: false },
    isSaved: { type: Boolean, default: false },
})

const { play } = usePlayback()
const saved = ref(props.isSaved)

const playAll = async () => {
    const songs = props.album.songs ?? []
    if (! songs.length) return
    await play(songs[0], songs)
}

const saveAlbum = async () => {
    try {
        const result = await toggleAlbum(props.album.id)
        saved.value = Boolean(result.saved)
    } catch (error) {
        console.error('No se pudo guardar el álbum', error)
    }
}

const remove = () => {
    if (! window.confirm(`¿Eliminar el álbum «${props.album.name}»?`)) return
    useForm({}).delete(route('albums.destroy', props.album.id))
}
</script>
