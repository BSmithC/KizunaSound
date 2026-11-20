<template>
    <AuthenticatedLayout>
        <Head title="Artistas" />

        <div class="ks-page">
            <SectionHeading eyebrow="Catálogo" title="Artistas" subtitle="Perfiles musicales con canciones publicadas.">
                <Link :href="route('artists.create')"
                    class="ks-btn ks-btn-primary">
                    Nuevo artista
                </Link>
            </SectionHeading>

            <div class="ks-card mb-6 p-4">
                <div class="grid gap-3 sm:grid-cols-3">
                    <input v-model="filtersForm.search" type="search" placeholder="Buscar artista..." class="form-input"
                        @keyup.enter="applyFilters" />
                    <select v-model="filtersForm.is_active" class="form-input">
                        <option value="">Todos los estados</option>
                        <option value="1">Activos</option>
                        <option value="0">Inactivos</option>
                    </select>
                    <div class="flex gap-2">
                        <button type="button" class="ks-btn ks-btn-primary flex-1"
                            @click="applyFilters">Filtrar</button>
                        <button type="button" class="rounded-full border border-warm/15 px-4 py-2.5 text-sm text-warm/75 hover:text-warm"
                            @click="clearFilters">Limpiar</button>
                    </div>
                </div>
            </div>

            <template v-if="artists.data?.length">
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                    <div v-for="artist in artists.data" :key="artist.id" class="min-w-0">
                        <Link :href="route('artists.show', artist.id)" class="block">
                            <ArtistCard :artist="artist" />
                        </Link>
                        <div class="mt-2 flex items-center justify-between gap-2 px-1">
                            <span class="text-[11px]" :class="artist.is_active ? 'text-emerald-400' : 'text-warm/45'">
                                {{ artist.is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                            <div class="flex flex-none gap-2">
                                <Link :href="route('artists.edit', artist.id)" class="text-xs text-warm/55 hover:text-warm">Editar</Link>
                                <button v-if="artist.user_id === authId" type="button" class="text-xs text-rose-400 hover:text-rose-300"
                                    @click="remove(artist)">Eliminar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between gap-4">
                    <span class="text-xs text-warm/45">Mostrando {{ artists.from ?? 0 }}–{{ artists.to ?? 0 }} de {{ artists.total ?? 0 }}</span>
                    <div class="flex gap-2">
                        <Link v-if="artists.prev_page_url" :href="artists.prev_page_url" class="rounded-full border border-warm/15 px-4 py-2 text-sm text-warm/75 hover:text-warm">Anterior</Link>
                        <Link v-if="artists.next_page_url" :href="artists.next_page_url" class="rounded-full border border-warm/15 px-4 py-2 text-sm text-warm/75 hover:text-warm">Siguiente</Link>
                    </div>
                </div>
            </template>

            <EmptyState v-else title="No hay artistas" description="Crea el primer perfil de artista del catálogo.">
                <template #action>
                    <Link :href="route('artists.create')" class="ks-btn ks-btn-primary">
                        Crear artista
                    </Link>
                </template>
            </EmptyState>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import SectionHeading from '@/Components/Music/SectionHeading.vue'
import ArtistCard from '@/Components/Music/ArtistCard.vue'
import EmptyState from '@/Components/Music/EmptyState.vue'

const props = defineProps({
    artists: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
})

const filtersForm = reactive({
    search: props.filters.search ?? '',
    is_active: props.filters.is_active ?? '',
})

const deleteForm = useForm({})
const authId = usePage().props.auth.user.id

const applyFilters = () => {
    router.get(route('artists.index'), {
        search: filtersForm.search,
        is_active: filtersForm.is_active,
    }, { preserveState: true, preserveScroll: true, replace: true })
}

const clearFilters = () => {
    filtersForm.search = ''
    filtersForm.is_active = ''
    applyFilters()
}

const remove = (artist) => {
    if (! window.confirm(`¿Eliminar el artista «${artist.name}»?`)) return
    deleteForm.delete(route('artists.destroy', artist.id), { preserveScroll: true })
}
</script>

<style scoped>
.form-input {
    @apply w-full rounded-lg border border-warm/10 bg-warm/5 px-3 py-2.5 text-sm text-warm placeholder-slate-500 outline-none transition focus:border-[var(--ks-accent)];
}
</style>
