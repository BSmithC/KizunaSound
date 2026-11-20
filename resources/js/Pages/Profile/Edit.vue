<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: { type: Boolean, default: false },
    status: { type: String, default: '' },
});
</script>

<template>
    <Head title="Perfil y configuración" />

    <AuthenticatedLayout>
        <div class="ks-page">
            <section class="ks-hero mb-10 p-6 sm:p-8">
                <p class="ks-eyebrow">設定 · Perfil y configuración</p>
                <h1 class="ks-title mt-2 text-3xl sm:text-4xl">Tu cuenta</h1>
                <p class="ks-subtitle mt-2">Administra tus datos personales, contraseña y preferencias.</p>
            </section>

            <div class="space-y-6">
                <div class="ks-card p-6 sm:p-8">
                    <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status"
                        class="max-w-xl" />
                </div>

                <div class="ks-card p-6 sm:p-8">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div class="ks-card p-6 sm:p-8">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
