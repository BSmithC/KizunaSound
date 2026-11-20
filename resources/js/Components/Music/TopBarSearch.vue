<template>
    <div class="relative w-full max-w-sm">
        <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-warm/40">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <circle cx="11" cy="11" r="7" /><path d="m20 20-3.5-3.5" stroke-linecap="round" />
            </svg>
        </div>
        <input v-model="query" type="search" :placeholder="placeholder"
            class="h-10 w-full rounded-full border border-warm/10 bg-warm/5 pl-9 pr-9 text-sm text-warm placeholder-warm/40 outline-none transition-colors duration-150 focus:border-[var(--ks-accent)] focus:bg-warm/10 focus:ring-2 focus:ring-shu/30"
            @input="debouncedSearch" @keyup.enter="submit" />
        <span v-if="loading" class="absolute inset-y-0 right-3 flex items-center text-[var(--ks-accent)]">
            <span class="h-3 w-3 animate-spin rounded-full border-2 border-current border-t-transparent" />
        </span>
        <button v-else-if="query" type="button" aria-label="Limpiar búsqueda"
            class="absolute inset-y-0 right-3 flex items-center text-warm/40 transition hover:text-warm" @click="clear">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="M6 6l12 12M18 6 6 18" stroke-linecap="round" />
            </svg>
        </button>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { useDebounceFn } from '@vueuse/core'
import { pushSearchHistory } from '@/helpers/searchHistory'

const props = defineProps({
    initial: { type: String, default: '' },
    placeholder: { type: String, default: 'Busca canciones, artistas, álbumes...' },
})

const query = ref(props.initial)
const loading = ref(false)

const runSearch = () => {
    loading.value = true
    router.get(route('search'), { q: query.value }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onFinish: () => { loading.value = false },
    })
}

const debouncedSearch = useDebounceFn(runSearch, 350)

const submit = () => {
    pushSearchHistory(query.value)
    loading.value = true
    router.get(route('search'), { q: query.value }, {
        preserveScroll: true,
        onFinish: () => { loading.value = false },
    })
}

const clear = () => {
    query.value = ''
    runSearch()
}
</script>
