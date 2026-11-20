<template>
    <AuthenticatedLayout>
        <Head title="Editar canción" />

        <div class="ks-page">
            <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="ks-eyebrow">Catálogo</p>
                    <h1 class="mt-2 text-3xl font-black tracking-tight text-warm">Editar canción</h1>
                    <p class="mt-1 text-sm text-warm/55">{{ song.name }}</p>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('songs.show', song.id)"
                        class="ks-btn ks-btn-ghost">Ver detalle</Link>
                    <button type="button" class="rounded-full border border-rose-500/40 px-5 py-2.5 text-sm text-rose-300 hover:bg-rose-500/10"
                        @click="remove">Eliminar</button>
                </div>
            </div>

            <form class="ks-card max-w-4xl space-y-5 p-6" @submit.prevent="submit">
                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="form-label" for="name">Título *</label>
                        <input id="name" v-model="form.name" type="text" class="form-input" />
                        <p v-if="form.errors.name" class="form-error">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="form-label" for="gender_id">Género *</label>
                        <select id="gender_id" v-model="form.gender_id" class="form-input">
                            <option v-for="gender in genders" :key="gender.id" :value="gender.id">{{ gender.name }}</option>
                        </select>
                        <p v-if="form.errors.gender_id" class="form-error">{{ form.errors.gender_id }}</p>
                    </div>

                    <div>
                        <label class="form-label" for="artist_ref_id">Artista del catálogo</label>
                        <select id="artist_ref_id" v-model="form.artist_ref_id" class="form-input">
                            <option value="">Sin artista asignado</option>
                            <option v-for="artist in artists" :key="artist.id" :value="artist.id">{{ artist.name }}</option>
                        </select>
                        <p v-if="form.errors.artist_ref_id" class="form-error">{{ form.errors.artist_ref_id }}</p>
                    </div>

                    <div>
                        <label class="form-label" for="mood">Mood</label>
                        <input id="mood" v-model="form.mood" type="text" list="mood-options" class="form-input" />
                        <datalist id="mood-options">
                            <option v-for="mood in moodOptions" :key="mood" :value="mood" />
                        </datalist>
                        <p v-if="form.errors.mood" class="form-error">{{ form.errors.mood }}</p>
                    </div>

                    <div>
                        <label class="form-label" for="license">Licencia</label>
                        <input id="license" v-model="form.license" type="text" class="form-input" />
                        <p v-if="form.errors.license" class="form-error">{{ form.errors.license }}</p>
                    </div>

                    <div>
                        <label class="form-label" for="release_date">Fecha de lanzamiento</label>
                        <input id="release_date" v-model="form.release_date" type="date" class="form-input" />
                        <p v-if="form.errors.release_date" class="form-error">{{ form.errors.release_date }}</p>
                    </div>

                    <div>
                        <label class="form-label" for="duration_seconds">Duración (segundos)</label>
                        <input id="duration_seconds" v-model="form.duration_seconds" type="number" min="0" class="form-input" />
                        <p v-if="form.errors.duration_seconds" class="form-error">{{ form.errors.duration_seconds }}</p>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="form-label" for="lyrics">Letra *</label>
                        <textarea id="lyrics" v-model="form.lyrics" rows="5" class="form-input"></textarea>
                        <p v-if="form.errors.lyrics" class="form-error">{{ form.errors.lyrics }}</p>
                    </div>

                    <div>
                        <label class="form-label" for="audio">Reemplazar audio (opcional)</label>
                        <input id="audio" type="file" accept="audio/mpeg,audio/wav,audio/ogg,audio/mp4" class="form-input"
                            @change="form.audio = $event.target.files[0] ?? null" />
                        <p class="mt-1 text-[11px] text-warm/45">Si no seleccionas un archivo se conserva el audio actual.</p>
                        <p v-if="form.errors.audio" class="form-error">{{ form.errors.audio }}</p>
                    </div>

                    <div>
                        <label class="form-label" for="thumbnail">Reemplazar portada (opcional)</label>
                        <input id="thumbnail" type="file" accept="image/*" class="form-input" @change="onThumbnail" />
                        <MediaThumb :src="thumbnailPreview ?? mediaUrl(song.thumbnail)" :alt="song.name" :label="initials(song.name)"
                            class="mt-3 h-24 w-24" />
                        <p v-if="form.errors.thumbnail" class="form-error">{{ form.errors.thumbnail }}</p>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="flex items-center gap-3 text-sm text-warm/75">
                            <input v-model="form.is_public" type="checkbox" class="h-4 w-4 accent-[var(--ks-accent)]" />
                            Publicar en el catálogo
                        </label>
                        <p v-if="form.errors.is_public" class="form-error">{{ form.errors.is_public }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 border-t border-warm/10 pt-5">
                    <button type="submit" :disabled="form.processing"
                        class="ks-btn ks-btn-primary disabled:opacity-50">
                        Guardar cambios
                    </button>
                    <Link :href="route('songs.index')"
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
import { mediaUrl, initials } from '@/helpers/media'

const props = defineProps({
    song: { type: Object, required: true },
    genders: { type: Array, default: () => [] },
    artists: { type: Array, default: () => [] },
    uploaders: { type: Array, default: () => [] },
    moodOptions: { type: Array, default: () => [] },
})

const thumbnailPreview = ref(null)

const form = useForm({
    name: props.song.name,
    gender_id: props.song.gender_id,
    artist_ref_id: props.song.artist_ref_id ?? '',
    lyrics: props.song.lyrics,
    thumbnail: null,
    audio: null,
    mood: props.song.mood ?? '',
    license: props.song.license ?? '',
    release_date: props.song.release_date?.slice(0, 10) ?? '',
    duration_seconds: props.song.duration_seconds ?? '',
    is_public: Boolean(props.song.is_public),
})

const onThumbnail = (event) => {
    const file = event.target.files[0] ?? null
    form.thumbnail = file
    thumbnailPreview.value = file ? URL.createObjectURL(file) : null
}

const submit = () => form.put(route('songs.update', props.song.id), { forceFormData: true })

const remove = () => {
    if (! window.confirm(`¿Eliminar la canción «${props.song.name}»?`)) return
    useForm({}).delete(route('songs.destroy', props.song.id))
}
</script>

<style scoped>
.form-label {
    @apply mb-2 block text-sm font-medium text-warm/75;
}

.form-input {
    @apply w-full rounded-lg border border-warm/10 bg-warm/5 px-3 py-2.5 text-sm text-warm placeholder-slate-500 outline-none transition focus:border-[var(--ks-accent)];
}

.form-error {
    @apply mt-1 text-xs text-rose-400;
}
</style>
