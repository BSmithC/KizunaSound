<template>
    <AuthenticatedLayout>
        <Head title="Nueva playlist" />

        <div class="ks-page">
            <div class="mb-8">
                <p class="ks-eyebrow">Tu música</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight text-warm">Nueva playlist</h1>
                <p class="mt-1 text-sm text-warm/55">Elige el nombre, la portada y las canciones de tu lista.</p>
            </div>

            <form class="ks-card max-w-5xl space-y-5 p-6" @submit.prevent="submit">
                <div class="grid gap-5 md:grid-cols-2">
                    <div class="space-y-5">
                        <div>
                            <label class="form-label" for="name">Nombre *</label>
                            <input id="name" v-model="form.name" type="text" class="form-input" placeholder="Mi playlist" />
                            <p v-if="form.errors.name" class="form-error">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <label class="form-label" for="description">Descripción</label>
                            <textarea id="description" v-model="form.description" rows="4" class="form-input"
                                placeholder="¿De qué va esta playlist?"></textarea>
                            <p v-if="form.errors.description" class="form-error">{{ form.errors.description }}</p>
                        </div>

                        <div>
                            <label class="form-label" for="img">Portada (opcional)</label>
                            <input id="img" type="file" accept="image/*" class="form-input" @change="onImage" />
                            <MediaThumb :src="coverPreview" alt="Vista previa" :label="initials(form.name || 'Playlist')"
                                class="mt-3 h-28 w-28" />
                            <p v-if="form.errors.img" class="form-error">{{ form.errors.img }}</p>
                        </div>

                        <div class="space-y-3">
                            <label class="flex items-center gap-3 text-sm text-warm/75">
                                <input v-model="form.is_public" type="checkbox" class="h-4 w-4 accent-[var(--ks-accent)]" />
                                Playlist pública
                            </label>
                            <label class="flex items-center gap-3 text-sm text-warm/75">
                                <input v-model="form.is_collaborative" type="checkbox" class="h-4 w-4 accent-[var(--ks-accent)]" />
                                Permitir colaboración
                            </label>
                        </div>
                    </div>

                    <div>
                        <div class="mb-3 flex items-center justify-between">
                            <span class="form-label mb-0">Canciones</span>
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
                        <EmptyState v-else title="No hay canciones disponibles" description="Sube canciones antes de crear una playlist." />
                        <p v-if="form.errors.songs" class="form-error">{{ form.errors.songs }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 border-t border-warm/10 pt-5">
                    <button type="submit" :disabled="form.processing"
                        class="ks-btn ks-btn-primary disabled:opacity-50">
                        Crear playlist
                    </button>
                    <Link :href="route('playlists.index')"
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
    songs: { type: Array, default: () => [] },
    playlist: { type: Object, default: null },
    selectedSongs: { type: Array, default: () => [] },
    canManage: { type: Boolean, default: true },
})

const coverPreview = ref(null)

const form = useForm({
    name: '',
    description: '',
    img: null,
    is_public: false,
    is_collaborative: false,
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

const submit = () => form.post(route('playlists.store'), { forceFormData: true })
</script>

<style scoped>
.form-label { @apply mb-2 block text-sm font-medium text-warm/75; }
.form-input { @apply w-full rounded-lg border border-warm/10 bg-warm/5 px-3 py-2.5 text-sm text-warm placeholder-slate-500 outline-none transition focus:border-[var(--ks-accent)]; }
.form-error { @apply mt-1 text-xs text-rose-400; }
</style>
