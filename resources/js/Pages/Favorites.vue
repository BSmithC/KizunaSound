<template>
    <AuthenticatedLayout>
        <Head title="Favoritos" />

        <div class="ks-page">
            <section class="ks-hero mb-10 p-6 sm:p-8">
                <p class="ks-eyebrow">お気に入り · Tu música</p>
                <h1 class="ks-title mt-2 text-3xl sm:text-4xl">Favoritos</h1>
                <p class="ks-subtitle mt-2">Las canciones que más te gustan.</p>
            </section>

            <TrackList v-if="songs.length" :songs="songs" />
            <EmptyState v-else title="Aún no tienes favoritos"
                description="Usa el botón de favorito en el reproductor o en una canción para guardarla aquí.">
                <template #action>
                    <Link :href="route('explore')"
                        class="ks-btn ks-btn-primary">
                        Explorar canciones
                    </Link>
                </template>
            </EmptyState>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import TrackList from '@/Components/Music/TrackList.vue'
import EmptyState from '@/Components/Music/EmptyState.vue'

defineProps({ songs: { type: Array, default: () => [] } })
</script>
