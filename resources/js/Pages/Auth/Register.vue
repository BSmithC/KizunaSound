<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="KizunaSound - 登録" />

    <div class="min-h-screen bg-gray-200 selection:bg-red-600 selection:text-white">
        <!-- Japanese wave pattern background -->
        <div class="fixed inset-0 opacity-10">
            <div class="absolute inset-0 bg-[radial-gradient(black_1px,transparent_1px)] [background-size:16px_16px]"></div>
        </div>

        <!-- Decorative music elements -->
        <div class="fixed top-20 left-10 text-red-600 opacity-20 text-5xl rotate-12 hidden lg:block">♪</div>
        <div class="fixed bottom-20 right-10 text-red-600 opacity-10 text-7xl -rotate-12 hidden lg:block">♫</div>
        <div class="fixed top-1/2 right-20 text-red-600 opacity-10 text-4xl hidden lg:block">🎵</div>

        <div class="relative flex min-h-screen flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div class="w-full max-w-md">
                <!-- Logo and Header -->
                <div class="text-center mb-8">
                    <div class="flex justify-center mb-4">
                        <div class="relative">
                            <div class="h-16 w-16 lg:h-20 lg:w-20 bg-red-600 rounded-full flex items-center justify-center shadow-lg">
                                <span class="text-white text-2xl lg:text-3xl font-bold">絆</span>
                            </div>
                            <div class="absolute -bottom-1 -right-1 text-red-600 text-sm">♪</div>
                        </div>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900">KizunaSound</h2>
                    <p class="mt-2 text-sm text-gray-600">音楽で繋がる · Crea tu cuenta musical</p>
                    <div class="flex justify-center gap-2 mt-3">
                        <div class="w-8 h-px bg-red-600/50"></div>
                        <div class="text-red-600 text-xs">♫</div>
                        <div class="w-8 h-px bg-red-600/50"></div>
                    </div>
                </div>

                <!-- Register Form -->
                <div class="bg-white rounded-lg shadow-xl p-8 border border-gray-200">
                    <form @submit.prevent="submit">
                        <div>
                            <InputLabel for="name" value="Nombre completo" class="text-gray-700" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full border-gray-300 focus:border-red-600 focus:ring-red-600"
                                v-model="form.name"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="Tu nombre"
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="email" value="Correo electrónico" class="text-gray-700" />
                            <TextInput
                                id="email"
                                type="email"
                                class="mt-1 block w-full border-gray-300 focus:border-red-600 focus:ring-red-600"
                                v-model="form.email"
                                required
                                autocomplete="username"
                                placeholder="tu@email.com"
                            />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="password" value="Contraseña" class="text-gray-700" />
                            <TextInput
                                id="password"
                                type="password"
                                class="mt-1 block w-full border-gray-300 focus:border-red-600 focus:ring-red-600"
                                v-model="form.password"
                                required
                                autocomplete="new-password"
                                placeholder="••••••••"
                            />
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="password_confirmation" value="Confirmar contraseña" class="text-gray-700" />
                            <TextInput
                                id="password_confirmation"
                                type="password"
                                class="mt-1 block w-full border-gray-300 focus:border-red-600 focus:ring-red-600"
                                v-model="form.password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="••••••••"
                            />
                            <InputError class="mt-2" :message="form.errors.password_confirmation" />
                        </div>

                        <div class="mt-6">
                            <PrimaryButton
                                class="w-full justify-center bg-red-600 hover:bg-red-700 focus:ring-red-600"
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                            >
                                <span class="flex items-center gap-2">
                                    <span>🎵</span>
                                    Crear Cuenta
                                    <span>🎵</span>
                                </span>
                            </PrimaryButton>
                        </div>

                        <div class="mt-4 text-center">
                            <Link
                                :href="route('login')"
                                class="text-sm text-gray-600 hover:text-red-600 hover:underline transition"
                            >
                                ¿Ya tienes cuenta? Inicia sesión
                            </Link>
                        </div>
                    </form>

                    <!-- Decorative line -->
                    <div class="mt-6 pt-4 text-center border-t border-gray-200">
                        <p class="text-xs text-gray-500">
                            <span class="text-red-600">♪</span> Únete a nuestra comunidad musical
                            <span class="text-red-600">♫</span>
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            Al registrarte, aceptas nuestros términos y condiciones
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
