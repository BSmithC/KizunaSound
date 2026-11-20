<script setup>
defineProps({
    message: { type: String, default: '' },
});
</script>

<template>
    <div v-show="message">
        <p class="form-error">{{ message }}</p>
    </div>
</template>
