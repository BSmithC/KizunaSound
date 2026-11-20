<template>
    <AuthenticatedLayout>
        <Head title="Playlists" />

        <div class="ks-page">
            <SectionHeading eyebrow="Tu música" title="Playlists" subtitle="Tus listas y las compartidas por la comunidad.">
                <Link :href="route('playlists.create')"
                    class="ks-btn ks-btn-primary">
                    Nueva playlist
                </Link>
            </SectionHeading>

            <div class="ks-card mb-6 p-4">
                <div class="grid gap-3 sm:grid-cols-3">
                    <input v-model="filtersForm.search" type="search" placeholder="Buscar playlist..." class="form-input"
                        @keyup.enter="applyFilters" />
                    <select v-model="filtersForm.is_public" class="form-input">
                        <option value="">Todas</option>
                        <option value="1">Públicas</option>
                        <option value="0">Privadas</option>
                    </select>
                    <div class="flex gap-2">
                        <button type="button" class="ks-btn ks-btn-primary flex-1"
                            @click="applyFilters">Filtrar</button>
                        <button type="button" class="rounded-full border border-warm/15 px-4 py-2.5 text-sm text-warm/75 hover:text-warm"
                            @click="clearFilters">Limpiar</button>
                    </div>
                </div>
            </div>

            <template v-if="playlists.data?.length">
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                    <div v-for="playlist in playlists.data" :key="playlist.id" class="min-w-0">
                        <Link :href="route('playlists.show', playlist.id)" class="block">
                            <PlaylistCard :playlist="playlist" />
                        </Link>
                        <div class="mt-2 flex items-center justify-between gap-2 px-1">
                            <span class="truncate text-[11px] text-warm/45">
                                {{ playlist.is_public ? 'Pública' : 'Privada' }} · {{ playlist.owner?.name ?? '—' }}
                            </span>
                            <div v-if="playlist.user_id === authId" class="flex flex-none gap-2">
                                <Link :href="route('playlists.edit', playlist.id)" class="text-xs text-warm/55 hover:text-warm">Editar</Link>
                                <button type="button" class="text-xs text-rose-400 hover:text-rose-300" @click="remove(playlist)">Eliminar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between gap-4">
                    <span class="text-xs text-warm/45">Mostrando {{ playlists.from ?? 0 }}–{{ playlists.to ?? 0 }} de {{ playlists.total ?? 0 }}</span>
                    <div class="flex gap-2">
                        <Link v-if="playlists.prev_page_url" :href="playlists.prev_page_url" class="rounded-full border border-warm/15 px-4 py-2 text-sm text-warm/75 hover:text-warm">Anterior</Link>
                        <Link v-if="playlists.next_page_url" :href="playlists.next_page_url" class="rounded-full border border-warm/15 px-4 py-2 text-sm text-warm/75 hover:text-warm">Siguiente</Link>
                    </div>
                </div>
            </template>

            <EmptyState v-else title="No hay playlists" description="Crea una playlist y añade tus canciones favoritas.">
                <template #action>
                    <Link :href="route('playlists.create')" class="ks-btn ks-btn-primary">
                        Crear playlist
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
import PlaylistCard from '@/Components/Music/PlaylistCard.vue'
import EmptyState from '@/Components/Music/EmptyState.vue'

const props = defineProps({
    playlists: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
})

const filtersForm = reactive({
    search: props.filters.search ?? '',
    is_public: props.filters.is_public ?? '',
})

const deleteForm = useForm({})
const authId = usePage().props.auth.user.id

const applyFilters = () => {
    router.get(route('playlists.index'), {
        search: filtersForm.search,
        is_public: filtersForm.is_public,
    }, { preserveState: true, preserveScroll: true, replace: true })
}

const clearFilters = () => {
    filtersForm.search = ''
    filtersForm.is_public = ''
    applyFilters()
}

const remove = (playlist) => {
    if (! window.confirm(`¿Eliminar la playlist «${playlist.name}»?`)) return
    deleteForm.delete(route('playlists.destroy', playlist.id), { preserveScroll: true })
}
</script>

<style scoped>
.form-input {
    @apply w-full rounded-lg border border-warm/10 bg-warm/5 px-3 py-2.5 text-sm text-warm placeholder-slate-500 outline-none transition focus:border-[var(--ks-accent)];
}
</style>
