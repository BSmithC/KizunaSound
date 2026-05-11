<template>
    <AuthenticatedLayout>
        <template #header>
            <div>
                <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                    Create Gender
                </h1>
            </div>
        </template>

        <div class="p-6 max-w-4xl mx-auto">
            <form @submit.prevent="submit">
                <div class="rounded-xl border-foregroundbg-background p-6 space-y-6">

                    <!-- Nombre -->
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <Label for="name">Name<span class="text-red-500"> *</span></Label>
                            <Input id="name" v-model="form.name" type="text" placeholder="Hip Hop" />
                        </div>

                        <div>
                            <Label for="description">Descriptión<span class="text-red-500"> *</span></Label>
                            <Textarea id="description" v-model="form.description" type="text"
                                placeholder="Detalles de la genero..." />
                        </div>
                    </div>





                    <!-- Acciones -->
                    <div class="flex justify-end gap-3">
                        <Button variant="outline" as-child>
                            <Link href="/genders">
                            Cancelar
                            </Link>
                        </Button>

                        <Button type="submit" :disabled="form.processing">
                            Guardar
                        </Button>
                    </div>

                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import Button from '@/Components/ui/button/Button.vue';
import Input from '@/Components/ui/input/Input.vue';
import Label from '@/Components/ui/label/Label.vue';
import InputError from '@/Components/InputError.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, useForm } from '@inertiajs/vue3';
import Textarea from '@/Components/ui/textarea/Textarea.vue';

export default {
    components: {
        AuthenticatedLayout,
        Input,
        Button,
        Label,
        Link,
        InputError,
        Textarea,
    },
    data() {
        return {
            form: useForm({
                name: '',
                description: '',
            })
        }
    },
    methods: {
        submit() {
            this.form.post('/genders');
        }
    }
}
</script>
