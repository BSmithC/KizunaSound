<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps({
    checked: { type: [Array, Boolean], required: true },
    value: { default: null },
});

const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit('update:checked', val);
    },
});
</script>

<template>
    <input
        type="checkbox"
        :value="value"
        v-model="proxyChecked"
        class="rounded border-warm/20 bg-sumi/60 text-[var(--ks-accent)] shadow-sm focus:ring-[var(--ks-accent)] dark:border-warm/20 dark:bg-sumi/60 dark:focus:ring-[var(--ks-accent)]"
    />
</template>
