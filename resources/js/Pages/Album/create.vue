<template>
    <AuthenticatedLayout>
        <Head :title="album ? 'Editar álbum' : 'Nuevo álbum'" />

        <div class="ks-page">
            <div class="mb-8">
                <p class="ks-eyebrow">Catálogo</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight text-warm">{{ album ? 'Editar álbum' : 'Nuevo álbum' }}</h1>
                <p class="mt-1 text-sm text-warm/55">Selecciona las canciones que formarán parte del lanzamiento.</p>
            </div>

            <form class="ks-card max-w-5xl space-y-5 p-6" @submit.prevent="submit">
                <div class="grid gap-5 md:grid-cols-2">
                    <div class="space-y-5">
                        <div>
                            <label class="form-label" for="name">Nombre *</label>
                            <input id="name" v-model="form.name" type="text" class="form-input" placeholder="Nombre del álbum" />
                            <p v-if="form.errors.name" class="form-error">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <label class="form-label" for="license">Licencia</label>
                            <input id="license" v-model="form.license" type="text" class="form-input" placeholder="CC BY 4.0" />
                            <p v-if="form.errors.license" class="form-error">{{ form.errors.license }}</p>
                        </div>

                        <div>
                            <label class="form-label" for="artist_id">Artista</label>
                            <select id="artist_id" v-model="form.artist_id" class="form-input">
                                <option value="">Sin artista asignado</option>
                                <option v-for="artist in artists" :key="artist.id" :value="artist.id">{{ artist.name }}</option>
                            </select>
                            <p v-if="form.errors.artist_id" class="form-error">{{ form.errors.artist_id }}</p>
                        </div>

                        <div>
                            <label class="form-label" for="release_date">Fecha de lanzamiento</label>
                            <input id="release_date" v-model="form.release_date" type="date" class="form-input" />
                            <p v-if="form.errors.release_date" class="form-error">{{ form.errors.release_date }}</p>
                        </div>

                        <div>
                            <label class="form-label" for="img">Portada (opcional)</label>
                            <input id="img" type="file" accept="image/*" class="form-input" @change="onImage" />
                            <MediaThumb :src="coverPreview ?? mediaUrl(album?.img)" :alt="form.name || 'Álbum'"
                                :label="initials(form.name || 'Álbum')" class="mt-3 h-28 w-28" />
                            <p v-if="form.errors.img" class="form-error">{{ form.errors.img }}</p>
                        </div>

                        <label class="flex items-center gap-3 text-sm text-warm/75">
                            <input v-model="form.is_public" type="checkbox" class="h-4 w-4 accent-[var(--ks-accent)]" />
                            Álbum público
                        </label>
                    </div>

                    <div>
                        <div class="mb-3 flex items-center justify-between">
                            <span class="form-label mb-0">Canciones del álbum</span>
                            <span class="text-xs text-warm/45">{{ form.songs.length }} seleccionadas</span>
                        </div>
                        <div v-if="songs.length" class="ks-scroll max-h-96 space-y-2 overflow-y-auto pr-1">
                            <label v-for="song in songs" :key="song.id"
                                class="flex cursor-pointer items-center gap-3 rounded-lg border px-3 py-2 transition"
                                :class="form.songs.includes(song.id) ? 'border-[var(--ks-accent)] bg-warm/5' : 'border-warm/10 hover:border-warm/20'">
                                <input type="checkbox" class="h-4 w-4 accent-[var(--ks-accent)]"
                                    :checked="form.songs.includes(song.id)" @change="toggleSong(song.id)" />
                                <MediaThumb :src="mediaUrl(song.thumbnail)" :alt="song.name" :label="initials(song.name)"
                                    rounded="rounded-md" class="h-9 w-9 flex-none" />
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate text-sm text-warm">{{ song.name }}</span>
                                    <span class="block truncate text-[11px] text-warm/45">{{ song.artist?.name ?? 'Artista desconocido' }}</span>
                                </span>
                                <span class="text-[11px] tabular-nums text-warm/45">{{ formatDuration(song.duration_seconds) }}</span>
                            </label>
                        </div>
                        <EmptyState v-else title="No hay canciones disponibles" description="Sube canciones antes de crear un álbum." />
                        <p v-if="form.errors.songs" class="form-error">{{ form.errors.songs }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 border-t border-warm/10 pt-5">
                    <button type="submit" :disabled="form.processing"
                        class="ks-btn ks-btn-primary disabled:opacity-50">
                        {{ album ? 'Guardar cambios' : 'Crear álbum' }}
                    </button>
                    <Link :href="album ? route('albums.show', album.id) : route('albums.index')"
                        class="ks-btn ks-btn-ghost">Cancelar</Link>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import MediaThumb from '@/Components/Music/MediaThumb.vue'
import EmptyState from '@/Components/Music/EmptyState.vue'
import { mediaUrl, initials, formatDuration } from '@/helpers/media'

const props = defineProps({
    album: { type: Object, default: null },
    songs: { type: Array, default: () => [] },
    artists: { type: Array, default: () => [] },
    selectedSongs: { type: Array, default: () => [] },
    canManage: { type: Boolean, default: true },
})

const coverPreview = ref(null)

const form = useForm({
    name: props.album?.name ?? '',
    license: props.album?.license ?? '',
    artist_id: props.album?.artist_id ?? '',
    release_date: props.album?.release_date?.slice(0, 10) ?? '',
    is_public: props.album ? Boolean(props.album.is_public) : true,
    img: null,
    songs: [...props.selectedSongs],
})

const onImage = (event) => {
    const file = event.target.files[0] ?? null
    form.img = file
    coverPreview.value = file ? URL.createObjectURL(file) : null
}

const toggleSong = (id) => {
    form.songs = form.songs.includes(id)
        ? form.songs.filter((songId) => songId !== id)
        : [...form.songs, id]
}

const submit = () => {
    const options = { forceFormData: true }
    if (props.album) {
        form.put(route('albums.update', props.album.id), options)
    } else {
        form.post(route('albums.store'), options)
    }
}
</script>

<style scoped>
.form-label { @apply mb-2 block text-sm font-medium text-warm/75; }
.form-input { @apply w-full rounded-lg border border-warm/10 bg-warm/5 px-3 py-2.5 text-sm text-warm placeholder-slate-500 outline-none transition focus:border-[var(--ks-accent)]; }
.form-error { @apply mt-1 text-xs text-rose-400; }
</style>
