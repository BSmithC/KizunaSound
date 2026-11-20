<template>
    <AuthenticatedLayout>
        <Head :title="artist ? 'Editar artista' : 'Nuevo artista'" />

        <div class="ks-page">
            <div class="mb-8">
                <p class="ks-eyebrow">Catálogo</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight text-warm">{{ artist ? 'Editar artista' : 'Nuevo artista' }}</h1>
                <p class="mt-1 text-sm text-warm/55">El slug se genera automáticamente a partir del nombre.</p>
            </div>

            <form class="ks-card max-w-3xl space-y-5 p-6" @submit.prevent="submit">
                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="form-label" for="name">Nombre *</label>
                        <input id="name" v-model="form.name" type="text" class="form-input" placeholder="Nombre artístico" />
                        <p v-if="form.errors.name" class="form-error">{{ form.errors.name }}</p>
                        <p v-if="artist?.slug" class="mt-1 text-[11px] text-warm/45">/{{ artist.slug }}</p>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="form-label" for="bio">Biografía</label>
                        <textarea id="bio" v-model="form.bio" rows="5" class="form-input"
                            placeholder="Cuéntanos sobre este artista..."></textarea>
                        <p v-if="form.errors.bio" class="form-error">{{ form.errors.bio }}</p>
                    </div>

                    <div>
                        <label class="form-label" for="image">Imagen de perfil</label>
                        <input id="image" type="file" accept="image/*" class="form-input" @change="onImage" />
                        <MediaThumb :src="imagePreview ?? mediaUrl(artist?.image)" :alt="form.name || 'Artista'"
                            :label="initials(form.name || 'Artista')" rounded="rounded-full" class="mt-3 h-24 w-24" />
                        <p v-if="form.errors.image" class="form-error">{{ form.errors.image }}</p>
                    </div>

                    <div>
                        <label class="form-label" for="cover">Imagen de portada</label>
                        <input id="cover" type="file" accept="image/*" class="form-input" @change="onCover" />
                        <MediaThumb :src="coverPreview ?? mediaUrl(artist?.cover)" :alt="`Portada de ${form.name || 'Artista'}`"
                            :label="initials(form.name || 'Artista')" class="mt-3 h-24 w-40" />
                        <p v-if="form.errors.cover" class="form-error">{{ form.errors.cover }}</p>
                    </div>

                    <label class="flex items-center gap-3 text-sm text-warm/75 sm:col-span-2">
                        <input v-model="form.is_active" type="checkbox" class="h-4 w-4 accent-[var(--ks-accent)]" />
                        Artista activo
                    </label>
                </div>

                <div class="flex flex-wrap gap-3 border-t border-warm/10 pt-5">
                    <button type="submit" :disabled="form.processing"
                        class="ks-btn ks-btn-primary disabled:opacity-50">
                        {{ artist ? 'Guardar cambios' : 'Crear artista' }}
                    </button>
                    <Link :href="artist ? route('artists.show', artist.id) : route('artists.index')"
                        class="ks-btn ks-btn-ghost">Cancelar</Link>
                    <button v-if="artist && canManage" type="button"
                        class="ml-auto rounded-full border border-rose-500/40 px-5 py-2.5 text-sm text-rose-300 hover:bg-rose-500/10"
                        @click="remove">Eliminar artista</button>
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
    artist: { type: Object, default: null },
    canManage: { type: Boolean, default: true },
})

const imagePreview = ref(null)
const coverPreview = ref(null)

const form = useForm({
    name: props.artist?.name ?? '',
    bio: props.artist?.bio ?? '',
    image: null,
    cover: null,
    is_active: props.artist ? Boolean(props.artist.is_active) : true,
})

const onImage = (event) => {
    const file = event.target.files[0] ?? null
    form.image = file
    imagePreview.value = file ? URL.createObjectURL(file) : null
}

const onCover = (event) => {
    const file = event.target.files[0] ?? null
    form.cover = file
    coverPreview.value = file ? URL.createObjectURL(file) : null
}

const submit = () => {
    const options = { forceFormData: true }
    if (props.artist) {
        form.put(route('artists.update', props.artist.id), options)
    } else {
        form.post(route('artists.store'), options)
    }
}

const remove = () => {
    if (! window.confirm(`¿Eliminar el artista «${props.artist.name}»?`)) return
    useForm({}).delete(route('artists.destroy', props.artist.id))
}
</script>

<style scoped>
.form-label { @apply mb-2 block text-sm font-medium text-warm/75; }
.form-input { @apply w-full rounded-lg border border-warm/10 bg-warm/5 px-3 py-2.5 text-sm text-warm placeholder-slate-500 outline-none transition focus:border-[var(--ks-accent)]; }
.form-error { @apply mt-1 text-xs text-rose-400; }
</style>
