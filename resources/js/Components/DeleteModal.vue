<script setup>
import { useForm } from '@inertiajs/vue3';
import BaseModal from './BaseModal.vue';

const props = defineProps({ personaje: { type: Object, default: null } });
const emit = defineEmits(['close']);
const form = useForm({});

function remove() {
    form.delete(`/personajes/${props.personaje.id}`, { preserveScroll: true, onSuccess: () => emit('close') });
}
</script>

<template>
    <BaseModal :open="!!personaje" :busy="form.processing" title="¿Eliminar personaje?" title-id="delete-title" @close="emit('close')">
        <p class="muted modal-description">Vas a eliminar a <strong>{{ personaje?.nombre }}</strong> de tu archivo. Esta acción no se puede deshacer.</p>
        <div class="modal-actions"><button class="button button-secondary" :disabled="form.processing" @click="emit('close')">Conservar personaje</button><button class="button button-danger" :disabled="form.processing" @click="remove">{{ form.processing ? 'Eliminando…' : 'Eliminar personaje' }}</button></div>
    </BaseModal>
</template>
