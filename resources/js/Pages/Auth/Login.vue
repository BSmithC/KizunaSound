<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>

    <Head title="KizunaSound - ログイン" />

    <div class="min-h-screen bg-gray-200 selection:bg-red-600 selection:text-white">
        <!-- Japanese wave pattern background -->
        <div class="fixed inset-0 opacity-10">
            <div class="absolute inset-0 bg-[radial-gradient(black_1px,transparent_1px)] [background-size:16px_16px]">
            </div>
        </div>

        <!-- Decorative music elements -->
        <div class="fixed top-20 left-10 text-red-600 opacity-20 text-5xl rotate-12 hidden lg:block">♪</div>
        <div class="fixed bottom-20 right-10 text-red-600 opacity-10 text-7xl -rotate-12 hidden lg:block">♫</div>

        <div class="relative flex min-h-screen flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div class="w-full max-w-md">
                <!-- Logo and Header -->
                <div class="text-center mb-8">
                    <div class="flex justify-center mb-4">
                        <div class="relative">
                            <div
                                class="h-16 w-16 lg:h-20 lg:w-20 bg-red-600 rounded-full flex items-center justify-center shadow-lg">
                                <span class="text-white text-2xl lg:text-3xl font-bold">絆</span>
                            </div>
                            <div class="absolute -bottom-1 -right-1 text-red-600 text-sm">♪</div>
                        </div>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900">KizunaSound</h2>
                    <p class="mt-2 text-sm text-gray-600">音楽で繋がる · Conéctate con la música</p>
                    <div class="flex justify-center gap-2 mt-3">
                        <div class="w-8 h-px bg-red-600/50"></div>
                        <div class="text-red-600 text-xs">♪</div>
                        <div class="w-8 h-px bg-red-600/50"></div>
                    </div>
                </div>

                <!-- Login Form -->
                <div class="bg-white rounded-lg shadow-xl p-8 border border-gray-200">
                    <div v-if="status" class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-700">
                        {{ status }}
                    </div>

                    <form @submit.prevent="submit">
                        <div>
                            <InputLabel for="email" value="Email" class="text-gray-700" />
                            <TextInput id="email" type="email"
                                class="mt-1 block w-full border-gray-300 focus:border-red-600 focus:ring-red-600"
                                v-model="form.email" required autofocus autocomplete="username" />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="password" value="Password" class="text-gray-700" />
                            <TextInput id="password" type="password"
                                class="mt-1 block w-full border-gray-300 focus:border-red-600 focus:ring-red-600"
                                v-model="form.password" required autocomplete="current-password" />
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <div class="mt-4 block">
                            <label class="flex items-center">
                                <Checkbox name="remember" v-model:checked="form.remember"
                                    class="rounded border-gray-300 text-red-600 focus:ring-red-600" />
                                <span class="ms-2 text-sm text-gray-600">Recordarme</span>
                            </label>
                        </div>

                        <div class="mt-6 flex flex-col space-y-3">
                            <PrimaryButton class="w-full justify-center bg-red-600 hover:bg-red-700 focus:ring-red-600"
                                :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                <span class="flex items-center gap-2">
                                    <span>♪</span>
                                    Iniciar Sesión
                                    <span>♫</span>
                                </span>
                            </PrimaryButton>

                            <div class="flex items-center justify-between text-sm">
                                <Link v-if="canResetPassword" :href="route('password.request')"
                                    class="text-red-600 hover:text-red-700 hover:underline transition">
                                    ¿Olvidaste tu contraseña?
                                </Link>

                                <Link :href="route('register')"
                                    class="text-gray-600 hover:text-red-600 hover:underline transition">
                                    ¿No tienes cuenta? Regístrate
                                </Link>
                            </div>
                        </div>
                    </form>

                    <!-- Decorative line -->
                    <div class="mt-6 pt-4 text-center border-t border-gray-200">
                        <p class="text-xs text-gray-500">
                            <span class="text-red-600">🎵</span> 音楽で繋がる
                            <span class="text-red-600">🎵</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
