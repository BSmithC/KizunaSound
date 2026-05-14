<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex">
                <h1 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Create Song
                </h1>
            </div>
        </template>
        <div class="p-6 max-w-7xl mx-auto">
            <form @submit.prevent="submit">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="mt-4">
                            <Label class="font-semibold" for="name">Name<span class="text-red-500"> *</span></Label>
                            <Input id="name" v-model="form.name" type="text" placeholder="Billie Jean" />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="mt-4">
                            <Select v-model="form.gender_id">
                                <Label class="font-semibold" for="gender">Select a Gender <span class="text-red-500">
                                        *</span></Label>
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Select a Gender" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="gender in genders" :key="gender.id" :value="gender.id">{{
                                        gender.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.gender_id" />
                        </div>

                        <div class="mt-4">
                            <Select v-model="form.artist_id">
                                <Label class="font-semibold" for="artist">Select a Artist <span class="text-red-500">
                                        *</span></Label>

                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Select a artist" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="aritst in artists" :key="aritst.id" :value="aritst.id">{{
                                        aritst.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.artist_id" />
                        </div>

                        <div class="mt-4">
                            <Label class="font-semibold" for="name">lyrics<span class="text-red-500">
                                    *</span></Label>
                            <Textarea id="name" v-model="form.lyrics" type="text" placeholder="Billie Jean is not my lover
She's just a girl who claims that I am the one (oh, baby)
But the kid is not my son (hoo)
She says I am the one (oh, baby)" />
                            <InputError :message="form.errors.lyrics" />
                        </div>
                        <div class="mt-4">
                            <Label class="font-semibold" for="audio">
                                Upload Audio <span class="text-red-500">*</span>
                            </Label>

                            <Input id="audio" type="file" accept="audio/*"
                                class="cursor-pointer bg-neutral-secondary-medium border border-default-medium text-heading text-sm text-gray-800 dark:text-gray-200 rounded-base focus:ring-brand focus:border-brand block w-full shadow-xs placeholder:text-body"
                                @change="handleAudio" />

                            <!-- Reproductor de vista previa opcional -->
                            <div v-if="audioPreview" class="mt-2">
                                <audio :src="audioPreview" controls class="w-full h-8"></audio>
                            </div>

                            <InputError :message="form.errors.audio" />
                        </div>

                    </div>
                    <div v-if="preview" class="mb-4">
                        <img :src="preview" class="h-full w-full object-fit-cover rounded-md border" />
                        <InputError :message="form.errors.img" />
                    </div>
                    <div v-else class="flex items-center justify-center w-full">
                        <Label for="thumbnail"
                            class="flex flex-col items-center justify-center w-full h-64 bg-neutral-secondary-medium border border-double border-default-strong rounded-base cursor-pointer hover:bg-neutral-tertiary-medium">
                            <div class="flex flex-col items-center justify-center text-body pt-5 pb-6">
                                <svg class="w-8 h-8 mb-4">...</svg>
                                <p class="mb-2 text-gray-800 dark:text-gray-200 text-sm">
                                    <span class="font-semibold">Click to upload</span> or drag and drop
                                </p>
                                <p class="text-gray-800 dark:text-gray-200 text-xs">
                                    SVG, PNG, JPG or GIF (MAX. 800x400px)
                                </p>
                            </div>
                            <Input id="thumbnail" type="file" class="hidden" accept="image/*" @change="handleImage" />
                        </Label>
                    </div>

                </div>

                <div class="flex justify-end gap-4 ">
                    <Button variant="outline" as-child>
                        <Link href="/songs">
                        Cancel
                        </Link>
                    </Button>

                    <Button variant="outline" type="submit">
                        Save
                    </Button>
                </div>
            </form>
        </div>

    </AuthenticatedLayout>
</template>

<script>
import AddIcon from '@/Components/Icons/AddIcon.vue';
import InputError from '@/Components/InputError.vue';
import Button from '@/Components/ui/button/Button.vue';
import Input from '@/Components/ui/input/Input.vue';
import Label from '@/Components/ui/label/Label.vue';
import Textarea from '@/Components/ui/textarea/Textarea.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Link, useForm } from '@inertiajs/vue3';

export default {
    components: {
        AuthenticatedLayout,
        Input,
        Label,
        Textarea,
        Link,
        Button,
        Select,
        SelectContent,
        SelectGroup,
        SelectItem,
        SelectLabel,
        SelectTrigger,
        SelectValue,
        InputError,
        AddIcon
    },
    props: {
        genders: Object,
        artists: Object,
        authId: Number,
    },
    data() {
        return {
            form: useForm({
                name: '',
                gender_id: '',
                // Cambiamos 'artists' por 'artist_id' para que coincida con tu validación en Laravel
                // Y le asignamos el valor de la prop authId por defecto
                artist_id: this.authId || '',
                thumbnail: null,
                lyrics: '',
                audio: '',
            }),
            preview: null,
            audioPreview: null,
        }
    },
    methods: {
        handleImage(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.form.thumbnail = file;
            this.preview = URL.createObjectURL(file);
        },
        handleAudio(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Guardamos el objeto de archivo real, no un string
            this.form.audio = file;

            // Solo para la interfaz, si quieres que se escuche
            this.audioPreview = URL.createObjectURL(file);
        },
        submit() {
            this.form.post('/songs');
        }
    }
}
</script>
