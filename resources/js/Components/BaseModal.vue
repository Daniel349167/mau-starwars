<script setup>
import { onMounted, ref, watch } from 'vue';
import AppIcon from './AppIcon.vue';

const props = defineProps({ open: Boolean, busy: Boolean, title: String, titleId: { type: String, required: true } });
const emit = defineEmits(['close']);
const dialog = ref(null);

function syncDialog() {
    if (!dialog.value) return;
    if (props.open && !dialog.value.open) dialog.value.showModal();
    if (!props.open && dialog.value.open) dialog.value.close();
}

function close() {
    if (!props.busy) emit('close');
}

watch(() => props.open, syncDialog);
onMounted(syncDialog);
</script>

<template>
    <dialog ref="dialog" class="modal" :aria-labelledby="titleId" @cancel.prevent="close">
        <div class="modal-heading"><h2 :id="titleId">{{ title }}</h2><button type="button" class="icon-button" aria-label="Cerrar" :disabled="busy" @click="close"><AppIcon name="close" /></button></div>
        <slot />
    </dialog>
</template>
