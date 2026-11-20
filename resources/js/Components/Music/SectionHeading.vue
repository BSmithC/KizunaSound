<template>
    <section class="ks-section">
        <div class="mb-5 flex items-end justify-between gap-4">
            <div class="min-w-0">
                <p v-if="eyebrow" class="ks-eyebrow mb-2 flex items-center gap-2">
                    <span class="inline-block h-3 w-0.5 bg-[var(--ks-accent)]"></span>
                    {{ eyebrow }}
                </p>
                <h2 class="ks-title text-xl text-warm sm:text-2xl">{{ title }}</h2>
                <p v-if="subtitle" class="ks-subtitle mt-1.5">{{ subtitle }}</p>
            </div>
            <slot name="action" />
        </div>
        <slot />
    </section>
</template>

<script setup>
defineProps({
    title: { type: String, required: true },
    eyebrow: { type: String, default: '' },
    subtitle: { type: String, default: '' },
})
</script>
