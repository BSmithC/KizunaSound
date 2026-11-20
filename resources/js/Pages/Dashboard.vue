<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Administración" />

    <AuthenticatedLayout>
        <div class="ks-page">
            <section class="ks-hero mb-10 p-6 sm:p-8">
                <p class="ks-eyebrow">管理 · Administración</p>
                <h1 class="ks-title mt-2 text-3xl sm:text-4xl">Panel de control</h1>
                <p class="ks-subtitle mt-2">Gestiona el catálogo musical y mantén tu biblioteca en orden.</p>
            </section>

            <div class="ks-card p-6">
                <h2 class="text-lg font-semibold text-warm">Sesión activa</h2>
                <p class="mt-2 text-sm text-warm/60">Has iniciado sesión correctamente en KizunaSound.</p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <Link :href="route('songs.index')" class="ks-btn ks-btn-primary">Gestionar canciones</Link>
                    <Link :href="route('albums.index')" class="ks-btn ks-btn-ghost">Gestionar álbumes</Link>
                    <Link :href="route('artists.index')" class="ks-btn ks-btn-ghost">Gestionar artistas</Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
