<template>
    <AuthenticatedLayout>
        <Head title="Géneros" />

        <div class="ks-page">
            <SectionHeading eyebrow="Taxonomía" title="Géneros" subtitle="Clasifica el catálogo musical por estilo.">
                <Link :href="route('genders.create')"
                    class="ks-btn ks-btn-primary">
                    Nuevo género
                </Link>
            </SectionHeading>

            <div class="ks-card mb-6 p-4">
                <div class="grid gap-3 sm:grid-cols-3">
                    <input v-model="filtersForm.search" type="search" placeholder="Buscar género..." class="form-input"
                        @keyup.enter="applyFilters" />
                    <select v-model="filtersForm.active" class="form-input">
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

            <div v-if="genders.data?.length" class="ks-card overflow-hidden">
                <div class="hidden grid-cols-[70px_1fr_1fr_110px_190px] gap-4 border-b border-warm/10 px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-warm/45 lg:grid">
                    <span>ID</span><span>Nombre</span><span>Descripción</span><span>Canciones</span><span class="text-right">Acciones</span>
                </div>
                <div v-for="gender in genders.data" :key="gender.id"
                    class="grid gap-3 border-b border-warm/10 px-5 py-4 last:border-0 lg:grid-cols-[70px_1fr_1fr_110px_190px] lg:items-center">
                    <span class="text-xs tabular-nums text-warm/45">#{{ gender.id }}</span>
                    <div class="min-w-0">
                        <Link :href="route('genders.show', gender.id)" class="truncate text-sm font-semibold text-warm hover:text-[var(--ks-accent)]">
                            {{ gender.name }}
                        </Link>
                        <p class="truncate text-[11px] text-warm/45">/{{ gender.slug }}</p>
                    </div>
                    <p class="truncate text-sm text-warm/55">{{ gender.description || '—' }}</p>
                    <div>
                        <span class="rounded-full px-2.5 py-1 text-[11px] font-medium"
                            :class="gender.active ? 'bg-emerald-500/10 text-emerald-300' : 'bg-slate-500/10 text-warm/55'">
                            {{ gender.active ? 'Activo' : 'Inactivo' }} · {{ gender.songs_count ?? 0 }}
                        </span>
                    </div>
                    <div class="flex flex-wrap justify-end gap-2">
                        <Link :href="route('genders.show', gender.id)" class="rounded-full border border-warm/15 px-3 py-1.5 text-xs text-warm/75 hover:text-warm">Ver</Link>
                        <Link :href="route('genders.edit', gender.id)" class="rounded-full border border-warm/15 px-3 py-1.5 text-xs text-warm/75 hover:text-warm">Editar</Link>
                        <button type="button" class="rounded-full border px-3 py-1.5 text-xs transition"
                            :class="gender.active ? 'border-amber-500/30 text-amber-300 hover:bg-amber-500/10' : 'border-emerald-500/30 text-emerald-300 hover:bg-emerald-500/10'"
                            @click="toggleActive(gender)">
                            {{ gender.active ? 'Desactivar' : 'Activar' }}
                        </button>
                    </div>
                </div>
            </div>

            <EmptyState v-else title="No hay géneros" description="Crea el primer género para organizar el catálogo.">
                <template #action>
                    <Link :href="route('genders.create')" class="ks-btn ks-btn-primary">
                        Nuevo género
                    </Link>
                </template>
            </EmptyState>

            <div v-if="genders.prev_page_url || genders.next_page_url" class="mt-6 flex justify-between">
                <Link v-if="genders.prev_page_url" :href="genders.prev_page_url" class="rounded-full border border-warm/15 px-4 py-2 text-sm text-warm/75 hover:text-warm">Anterior</Link>
                <Link v-if="genders.next_page_url" :href="genders.next_page_url" class="rounded-full border border-warm/15 px-4 py-2 text-sm text-warm/75 hover:text-warm">Siguiente</Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import SectionHeading from '@/Components/Music/SectionHeading.vue'
import EmptyState from '@/Components/Music/EmptyState.vue'

const props = defineProps({
    genders: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
})

const filtersForm = reactive({
    search: props.filters.search ?? '',
    active: props.filters.active ?? '',
})

const toggleForm = useForm({})

const applyFilters = () => {
    router.get(route('genders.index'), {
        search: filtersForm.search,
        active: filtersForm.active,
    }, { preserveState: true, preserveScroll: true, replace: true })
}

const clearFilters = () => {
    filtersForm.search = ''
    filtersForm.active = ''
    applyFilters()
}

const toggleActive = (gender) => {
    toggleForm.delete(route('genders.destroy', gender.id), { preserveScroll: true })
}
</script>

<style scoped>
.form-input {
    @apply w-full rounded-lg border border-warm/10 bg-warm/5 px-3 py-2.5 text-sm text-warm placeholder-slate-500 outline-none transition focus:border-[var(--ks-accent)];
}
</style>
