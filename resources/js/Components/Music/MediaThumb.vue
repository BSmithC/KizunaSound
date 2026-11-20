<template>
    <div class="relative overflow-hidden bg-charcoal" :class="rounded">
        <img v-if="src && !failed" :src="src" :alt="alt" loading="lazy" class="h-full w-full object-cover"
            @error="failed = true" />
        <div v-else
            class="relative flex h-full w-full items-center justify-center bg-gradient-to-br from-moss/25 via-charcoal to-sumi">
            <span class="relative z-10 text-2xl font-black tracking-tight text-warm/70">{{ label }}</span>
            <div class="ks-motif ks-motif--washi opacity-70"></div>
        </div>
        <slot />
    </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { initials } from '@/helpers/media'

const props = defineProps({
    src: { type: String, default: null },
    alt: { type: String, default: '' },
    label: { type: String, default: 'K' },
    rounded: { type: String, default: 'rounded-xl' },
})

const failed = ref(false)
watch(() => props.src, () => { failed.value = false })
</script>
