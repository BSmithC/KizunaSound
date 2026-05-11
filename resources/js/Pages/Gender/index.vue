<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex-auto">
                <h1 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Gender
                </h1>
            </div>
            <Button as-child>
                <Link href="/genders/create">
                New Gender
                <AddIcon />
                </Link>
            </Button>
        </template>

        <div class="mt-4 mx-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="w-full sm:max-w-sm">
                <Input v-model="search" placeholder="Filter Gender..." />
            </div>
        </div>
        <div class="p-6">
            <div class="rounded-xl border-foreground bg-background shadow">
                <div class="mb:block overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead class="text-center font-bold">#</TableHead>
                                <TableHead class="text-center">Name</TableHead>
                                <TableHead class="text-center">Description</TableHead>
                                <TableHead class="text-center">Action</TableHead>
                            </TableRow>
                        </TableHeader>

                        <TableBody>
                            <TableRow v-for="gender in genders.data" :key="gender.id">
                                <TableCell class="font-bold text-center">
                                    {{ gender.id }}
                                </TableCell>
                                <TableCell class="text-center">
                                    {{ gender.name }}
                                </TableCell>
                                <TableCell class="text-center">
                                    {{ gender.description }}
                                </TableCell>
                                <TableCell class="text-center">
                                    <Button size="sm" as-child
                                        class="hover:bg-yellow-600 bg-yellow-500 text-white flex-1">
                                        <Link :href="`/genders/${gender.id}/edit`"
                                            class="flex items-center justify-center gap-1">
                                        <EditIcon class="h-4 w-4" />
                                        </Link>
                                    </Button>
                                    <Button size="sm" as-child class="hover:bg-red-600 bg-red-500 text-white">
                                        <Link :href="`/genders/${gender.id}/edit`">
                                        <DeleteIcon class="h-4 w-4" />
                                        </Link>
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AddIcon from '@/Components/Icons/AddIcon.vue';
import DeleteIcon from '@/Components/Icons/DeleteIcon.vue';
import EditIcon from '@/Components/Icons/EditIcon.vue';
import EyeIcon from '@/Components/Icons/EyeIcon.vue';
import Button from '@/Components/ui/button/Button.vue';
import Input from '@/Components/ui/input/Input.vue';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, router, useForm } from '@inertiajs/vue3';

export default {
    components: {
        AuthenticatedLayout,
        Button,
        Link,
        AddIcon,
        Input,
        Table,
        TableHeader,
        TableHead,
        TableRow,
        TableBody,
        TableCell,
        EditIcon,
        DeleteIcon
    },
    props: {
        genders: Object,
        filters: Object
    },
    data() {
        return {
            search: this.filters?.search || '',
        }
    },
    watch: {
        search() {
            this.applyFilters()
        }
    },
    methods: {
        applyFilters() {
            router.get(
                '/genders',
                {
                    search: this.search,
                },
                {
                    preserveState: true,
                    replace: true,
                    preserveScroll: true,
                }
            )
        }
    }
}
</script>
