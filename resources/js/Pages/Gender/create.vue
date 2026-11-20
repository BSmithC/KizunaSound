<template>
    <AuthenticatedLayout>
        <Head title="Nuevo género" />

        <div class="ks-page">
            <SectionHeading class="mb-8" eyebrow="Taxonomía" title="Nuevo género"
                subtitle="El slug se genera automáticamente a partir del nombre." />

            <form class="ks-card max-w-2xl space-y-5 p-6" @submit.prevent="submit">
                <div>
                    <label class="form-label" for="name">Nombre *</label>
                    <input id="name" v-model="form.name" type="text" class="form-input" placeholder="Ej. Synthwave" />
                    <p v-if="form.errors.name" class="form-error">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label class="form-label" for="description">Descripción</label>
                    <textarea id="description" v-model="form.description" rows="4" class="form-input"
                        placeholder="Describe las características del género..."></textarea>
                    <p v-if="form.errors.description" class="form-error">{{ form.errors.description }}</p>
                </div>

                <label class="flex items-center gap-3 text-sm text-warm/75">
                    <input v-model="form.active" type="checkbox" class="h-4 w-4 accent-[var(--ks-accent)]" />
                    Género activo
                </label>

                <div class="flex flex-wrap gap-3 border-t border-warm/10 pt-5">
                    <button type="submit" :disabled="form.processing"
                        class="ks-btn ks-btn-primary disabled:opacity-50">
                        Guardar género
                    </button>
                    <Link :href="route('genders.index')"
                        class="ks-btn ks-btn-ghost">Cancelar</Link>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import SectionHeading from '@/Components/Music/SectionHeading.vue'

const form = useForm({
    name: '',
    description: '',
    active: true,
})

const submit = () => form.post(route('genders.store'))
</script>

<style scoped>
.form-label { @apply mb-2 block text-sm font-medium text-warm/75; }
.form-input { @apply w-full rounded-lg border border-warm/10 bg-warm/5 px-3 py-2.5 text-sm text-warm placeholder-slate-500 outline-none transition focus:border-[var(--ks-accent)]; }
.form-error { @apply mt-1 text-xs text-rose-400; }
</style>
