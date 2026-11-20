<script setup>
defineProps({
    type: { type: String, default: 'button' },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center justify-center rounded-lg border border-warm/15 bg-warm/5 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-warm/70 transition duration-150 hover:border-[var(--ks-accent)] hover:text-warm disabled:opacity-25"
    >
        <slot />
    </button>
</template>
