<template>
    <AuthenticatedLayout>
        <Head title="Canciones" />

        <div class="ks-page">
            <SectionHeading eyebrow="Catálogo" title="Canciones"
                subtitle="Busca, filtra y administra las pistas subidas a la plataforma.">
                <Link :href="route('songs.create')"
                    class="ks-btn ks-btn-primary">
                    Nueva canción
                </Link>
            </SectionHeading>

            <div class="ks-card mb-6 p-4">
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <input v-model="filtersForm.search" type="search" placeholder="Buscar por título, letra o artista..."
                        class="w-full rounded-lg border border-warm/10 bg-warm/5 px-3 py-2.5 text-sm text-warm placeholder-slate-500 outline-none focus:border-[var(--ks-accent)]"
                        @keyup.enter="applyFilters" />
                    <select v-model="filtersForm.gender_id"
                        class="w-full rounded-lg border border-warm/10 bg-sumi px-3 py-2.5 text-sm text-warm outline-none focus:border-[var(--ks-accent)]">
                        <option value="">Todos los géneros</option>
                        <option v-for="gender in genders" :key="gender.id" :value="gender.id">{{ gender.name }}</option>
                    </select>
                    <select v-model="filtersForm.mood"
                        class="w-full rounded-lg border border-warm/10 bg-sumi px-3 py-2.5 text-sm text-warm outline-none focus:border-[var(--ks-accent)]">
                        <option value="">Todos los moods</option>
                        <option v-for="mood in moodOptions" :key="mood" :value="mood">{{ mood }}</option>
                    </select>
                    <div class="flex gap-2">
                        <button type="button"
                            class="ks-btn ks-btn-primary flex-1 transition hover:brightness-110"
                            @click="applyFilters">Filtrar</button>
                        <button type="button"
                            class="rounded-full border border-warm/15 px-4 py-2.5 text-sm text-warm/75 transition hover:text-warm"
                            @click="clearFilters">Limpiar</button>
                    </div>
                </div>
            </div>

            <template v-if="songs.data?.length">
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                    <div v-for="song in songs.data" :key="song.id" class="min-w-0">
                        <SongCard :song="song" :queue="songs.data" />
                        <div class="mt-2 flex items-center justify-between gap-2 px-1">
                            <span class="truncate text-[11px] text-warm/45">{{ formatDuration(song.duration_seconds) }}</span>
                            <div class="flex flex-none items-center gap-2">
                                <Link :href="route('songs.edit', song.id)" class="text-xs text-warm/55 hover:text-warm">Editar</Link>
                                <button type="button" class="text-xs text-rose-400 hover:text-rose-300" @click="remove(song)">Eliminar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <SectionHeading title="Vista de lista" subtitle="Reproduce cualquier pista con el reproductor global.">
                        <TrackList :songs="songs.data" />
                    </SectionHeading>
                </div>

                <div class="mt-6 flex items-center justify-between gap-4">
                    <span class="text-xs text-warm/45">
                        Mostrando {{ songs.from ?? 0 }}–{{ songs.to ?? 0 }} de {{ songs.total ?? 0 }}
                    </span>
                    <div class="flex gap-2">
                        <Link v-if="songs.prev_page_url" :href="songs.prev_page_url"
                            class="rounded-full border border-warm/15 px-4 py-2 text-sm text-warm/75 hover:text-warm">Anterior</Link>
                        <Link v-if="songs.next_page_url" :href="songs.next_page_url"
                            class="rounded-full border border-warm/15 px-4 py-2 text-sm text-warm/75 hover:text-warm">Siguiente</Link>
                    </div>
                </div>
            </template>

            <EmptyState v-else title="No hay canciones" description="Ajusta los filtros o sube la primera pista al catálogo.">
                <template #action>
                    <Link :href="route('songs.create')"
                        class="ks-btn ks-btn-primary">Subir canción</Link>
                </template>
            </EmptyState>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import SectionHeading from '@/Components/Music/SectionHeading.vue'
import SongCard from '@/Components/Music/SongCard.vue'
import TrackList from '@/Components/Music/TrackList.vue'
import EmptyState from '@/Components/Music/EmptyState.vue'
import { formatDuration } from '@/helpers/media'

const props = defineProps({
    songs: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
    genders: { type: Array, default: () => [] },
    moodOptions: { type: Array, default: () => [] },
})

const filtersForm = reactive({
    search: props.filters.search ?? '',
    gender_id: props.filters.gender_id ?? '',
    mood: props.filters.mood ?? '',
})

const deleteForm = useForm({})

const applyFilters = () => {
    router.get(route('songs.index'), {
        search: filtersForm.search,
        gender_id: filtersForm.gender_id,
        mood: filtersForm.mood,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

const clearFilters = () => {
    filtersForm.search = ''
    filtersForm.gender_id = ''
    filtersForm.mood = ''
    applyFilters()
}

const remove = (song) => {
    if (! window.confirm(`¿Eliminar la canción «${song.name}»?`)) return
    deleteForm.delete(route('songs.destroy', song.id), { preserveScroll: true })
}
</script>
