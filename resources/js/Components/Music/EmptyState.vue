<template>
    <div class="ks-card flex flex-col items-center justify-center overflow-hidden px-6 py-12 text-center">
        <div class="ks-motif ks-motif--seigaiha opacity-40"></div>
        <div class="relative mb-4 flex h-12 w-12 items-center justify-center rounded-full border border-warm/10 bg-warm/5 text-[var(--ks-accent)]">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                <path d="M9 18V5l10-2v13" stroke-linecap="round" stroke-linejoin="round" />
                <circle cx="6" cy="18" r="3" /><circle cx="16" cy="16" r="3" />
            </svg>
        </div>
        <h3 class="relative text-base font-semibold text-warm">{{ title }}</h3>
        <p v-if="description" class="relative mt-2 max-w-md text-sm text-warm/55">{{ description }}</p>
        <div v-if="$slots.action" class="relative mt-5"><slot name="action" /></div>
    </div>
</template>

<script setup>
defineProps({
    title: { type: String, default: 'Sin resultados' },
    description: { type: String, default: '' },
})
</script>
