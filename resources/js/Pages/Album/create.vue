<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex">
                <h1 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Create Album
                </h1>
            </div>
        </template>

        <div class="p-6 max-w-7xl mx-auto">
            <form @submit.prevent="submit">
                <div class="grid grid-cols-2 gap-4">

                    <div class="mt-4">
                        <Label class="font-semibold" for="name">Name<span class="text-red-500"> *</span></Label>
                        <Input id="name" v-model="form.name" type="text" placeholder="Billie Jean" />
                        <!-- <InputError :message="form.errors.name" /> -->
                    </div>

                    <div class="mt-4">
                        <Label class="font-semibold" for="license">License<span class="text-red-500"> *</span></Label>
                        <Input id="license" v-model="form.license" type="text" placeholder="Billie Jean" />
                        <!-- <InputError :message="form.errors.license" /> -->
                    </div>
                </div>
                <div class="mt-4">
                    <Select v-model="form.song_id">
                        <Label class="font-semibold" for="gender">Select A Song <span class="text-red-500">
                                *</span></Label>
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select A Song" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="song in songs" :key="song.id" :value="song.id">{{
                                song.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <!-- <InputError :message="form.errors.song_id" /> -->
                </div>

                <div class="flex justify-end gap-4 mt-4">
                    <Button variant="outline" as-child>
                        <Link :href="`/albums`">
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
        songs: Object,
    },
    data() {
        return {
            form: {
                name: '',
                license: '',
                song_id: '',
            }
        }
    },
    methods: {
        submit(){
            this.form.post('/albums');
        }
    }
}
</script>
