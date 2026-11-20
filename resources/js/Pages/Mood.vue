<template>
    <AuthenticatedLayout>
        <Head title="Por estado de ánimo" />

        <div class="ks-page">
            <section class="ks-hero mb-10 p-6 sm:p-8">
                <p class="ks-eyebrow">情緒 · Descubre</p>
                <h1 class="ks-title mt-2 text-3xl sm:text-4xl">Música por estado de ánimo</h1>
                <p class="ks-subtitle mt-2 max-w-2xl">
                    Agrupaciones basadas en el estado de ánimo asignado a cada canción del catálogo real.
                </p>
            </section>

            <template v-if="moods.length">
                <SectionHeading v-for="group in moods" :key="group.mood" :title="group.mood"
                    :subtitle="`${group.songs.length} canción${group.songs.length === 1 ? '' : 'es'}`">
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                        <SongCard v-for="song in group.songs" :key="song.id" :song="song" :queue="group.songs" />
                    </div>
                </SectionHeading>
            </template>

            <EmptyState v-else title="Aún no hay estados de ánimo asignados"
                description="Cuando las canciones del catálogo tengan un estado de ánimo, aparecerán agrupadas aquí.">
                <template #action>
                    <Link :href="route('songs.index')"
                        class="ks-btn ks-btn-primary">
                        Ir al catálogo
                    </Link>
                </template>
            </EmptyState>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import SectionHeading from '@/Components/Music/SectionHeading.vue'
import SongCard from '@/Components/Music/SongCard.vue'
import EmptyState from '@/Components/Music/EmptyState.vue'

defineProps({
    moods: { type: Array, default: () => [] },
})
</script>
