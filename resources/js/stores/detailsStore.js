import { defineStore } from 'pinia'
import { computed, ref } from 'vue'

/**
 * Estado de UI del panel lateral de detalles.
 * Es totalmente independiente del store del reproductor: abrir, cerrar o
 * cambiar la selección nunca modifica la reproducción.
 */
export const useDetailsStore = defineStore('details', () => {
    const selection = ref(null)
    const isOpen = ref(false)

    const type = computed(() => selection.value?.type ?? null)
    const item = computed(() => selection.value?.item ?? null)

    function open(selectedType, selectedItem) {
        selection.value = { type: selectedType, item: selectedItem ?? null }
        isOpen.value = true
    }

    function openQueue() {
        open('queue', null)
    }

    function close() {
        isOpen.value = false
    }

    function toggle() {
        if (isOpen.value) close()
        else if (selection.value) isOpen.value = true
        else openQueue()
    }

    return {
        selection,
        isOpen,
        type,
        item,
        open,
        openQueue,
        close,
        toggle,
    }
})
