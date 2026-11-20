<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: { type: String, required: true },
    active: { type: Boolean, default: false },
});

const classes = computed(() =>
    props.active
        ? 'block w-full border-l-4 border-[var(--ks-accent)] bg-shu/10 px-3 py-2 text-start text-sm font-medium text-warm transition duration-150'
        : 'block w-full border-l-4 border-transparent px-3 py-2 text-start text-sm font-medium text-warm/55 transition duration-150 hover:border-warm/20 hover:bg-warm/5 hover:text-warm',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
