<template>
    <AuthenticatedLayout>
        <Head :title="gender.name" />

        <div class="ks-page">
            <div class="mb-6">
                <Link :href="route('genders.index')" class="text-sm text-warm/55 hover:text-warm">← Volver a géneros</Link>
            </div>

            <section class="ks-hero mb-8 flex flex-wrap items-start justify-between gap-5 p-6">
                <div>
                    <p class="ks-eyebrow">Género</p>
                    <h1 class="mt-2 text-3xl font-black tracking-tight text-warm">{{ gender.name }}</h1>
                    <p class="mt-1 text-sm text-warm/55">/{{ gender.slug }}</p>
                    <p class="mt-4 max-w-2xl text-sm leading-relaxed text-warm/75">{{ gender.description || 'Sin descripción.' }}</p>
                    <div class="mt-4 flex flex-wrap gap-2 text-xs">
                        <span class="rounded-full border border-warm/10 bg-warm/5 px-3 py-1 text-warm/75">
                            {{ gender.songs_count ?? 0 }} canciones
                        </span>
                        <span class="rounded-full border px-3 py-1"
                            :class="gender.active ? 'border-emerald-500/30 text-emerald-300' : 'border-slate-500/30 text-warm/55'">
                            {{ gender.active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <Link :href="route('genders.edit', gender.id)"
                        class="ks-btn ks-btn-ghost">Editar</Link>
                    <button type="button"
                        class="rounded-full border border-amber-500/30 px-5 py-2.5 text-sm text-amber-300 hover:bg-amber-500/10"
                        @click="toggleActive">
                        {{ gender.active ? 'Desactivar' : 'Activar' }}
                    </button>
                </div>
            </section>

            <SectionHeading title="Canciones de este género" subtitle="Solo se muestran las canciones públicas.">
                <TrackList v-if="gender.songs?.length" :songs="gender.songs" />
                <EmptyState v-else title="Sin canciones" description="Aún no hay pistas públicas asociadas a este género." />
            </SectionHeading>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import SectionHeading from '@/Components/Music/SectionHeading.vue'
import TrackList from '@/Components/Music/TrackList.vue'
import EmptyState from '@/Components/Music/EmptyState.vue'

const props = defineProps({
    gender: { type: Object, required: true },
    canManage: { type: Boolean, default: false },
})

const toggleForm = useForm({})

const toggleActive = () => {
    toggleForm.delete(route('genders.destroy', props.gender.id), { preserveScroll: true })
}
</script>
