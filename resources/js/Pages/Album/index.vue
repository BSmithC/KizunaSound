<template>
    <AuthenticatedLayout>
        <Head title="Álbumes" />

        <div class="ks-page">
            <SectionHeading eyebrow="Catálogo" title="Álbumes" subtitle="Agrupa canciones en lanzamientos completos.">
                <Link :href="route('albums.create')"
                    class="ks-btn ks-btn-primary">
                    Nuevo álbum
                </Link>
            </SectionHeading>

            <div class="ks-card mb-6 p-4">
                <div class="grid gap-3 sm:grid-cols-3">
                    <input v-model="filtersForm.search" type="search" placeholder="Buscar álbum o artista..." class="form-input"
                        @keyup.enter="applyFilters" />
                    <select v-model="filtersForm.is_public" class="form-input">
                        <option value="">Todos los álbumes</option>
                        <option value="1">Públicos</option>
                        <option value="0">Privados</option>
                    </select>
                    <div class="flex gap-2">
                        <button type="button" class="ks-btn ks-btn-primary flex-1"
                            @click="applyFilters">Filtrar</button>
                        <button type="button" class="rounded-full border border-warm/15 px-4 py-2.5 text-sm text-warm/75 hover:text-warm"
                            @click="clearFilters">Limpiar</button>
                    </div>
                </div>
            </div>

            <template v-if="albums.data?.length">
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                    <div v-for="album in albums.data" :key="album.id" class="min-w-0">
                        <Link :href="route('albums.show', album.id)" class="block">
                            <AlbumCard :album="album" />
                        </Link>
                        <div class="mt-2 flex items-center justify-between gap-2 px-1">
                            <span class="text-[11px] text-warm/45">{{ album.songs_count ?? 0 }} pistas</span>
                            <div class="flex flex-none gap-2">
                                <Link :href="route('albums.edit', album.id)" class="text-xs text-warm/55 hover:text-warm">Editar</Link>
                                <button type="button" class="text-xs text-rose-400 hover:text-rose-300" @click="remove(album)">Eliminar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between gap-4">
                    <span class="text-xs text-warm/45">Mostrando {{ albums.from ?? 0 }}–{{ albums.to ?? 0 }} de {{ albums.total ?? 0 }}</span>
                    <div class="flex gap-2">
                        <Link v-if="albums.prev_page_url" :href="albums.prev_page_url" class="rounded-full border border-warm/15 px-4 py-2 text-sm text-warm/75 hover:text-warm">Anterior</Link>
                        <Link v-if="albums.next_page_url" :href="albums.next_page_url" class="rounded-full border border-warm/15 px-4 py-2 text-sm text-warm/75 hover:text-warm">Siguiente</Link>
                    </div>
                </div>
            </template>

            <EmptyState v-else title="No hay álbumes" description="Prueba con otros filtros o crea el primer álbum.">
                <template #action>
                    <Link :href="route('albums.create')" class="ks-btn ks-btn-primary">
                        Crear álbum
                    </Link>
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
import AlbumCard from '@/Components/Music/AlbumCard.vue'
import EmptyState from '@/Components/Music/EmptyState.vue'

const props = defineProps({
    albums: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
})

const filtersForm = reactive({
    search: props.filters.search ?? '',
    is_public: props.filters.is_public ?? '',
})

const deleteForm = useForm({})

const applyFilters = () => {
    router.get(route('albums.index'), {
        search: filtersForm.search,
        is_public: filtersForm.is_public,
    }, { preserveState: true, preserveScroll: true, replace: true })
}

const clearFilters = () => {
    filtersForm.search = ''
    filtersForm.is_public = ''
    applyFilters()
}

const remove = (album) => {
    if (! window.confirm(`¿Eliminar el álbum «${album.name}»?`)) return
    deleteForm.delete(route('albums.destroy', album.id), { preserveScroll: true })
}
</script>

<style scoped>
.form-input {
    @apply w-full rounded-lg border border-warm/10 bg-warm/5 px-3 py-2.5 text-sm text-warm placeholder-slate-500 outline-none transition focus:border-[var(--ks-accent)];
}
</style>
