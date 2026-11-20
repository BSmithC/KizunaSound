<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: { type: String, required: true },
});
</script>

<template>
    <Link
        :href="href"
        class="block w-full px-4 py-2 text-start text-sm leading-5 text-warm/70 transition duration-150 hover:bg-shu/10 hover:text-warm focus:bg-shu/10 focus:outline-none"
    >
        <slot />
    </Link>
</template>
