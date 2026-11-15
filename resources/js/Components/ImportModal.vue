<script setup>
import { useForm } from '@inertiajs/vue3';
import BaseModal from './BaseModal.vue';
import AppIcon from './AppIcon.vue';

defineProps({ open: Boolean });
const emit = defineEmits(['close']);
const form = useForm({ swapi_id: '' });

function close() {
    if (form.processing) return;
    form.reset();
    form.clearErrors();
    emit('close');
}

function submit() {
    form.post('/personajes/importar', { preserveScroll: true, onSuccess: close });
}
</script>

<template>
    <BaseModal :open="open" :busy="form.processing" title="Importar desde SWAPI" title-id="import-title" @close="close">
        <p class="muted modal-description">Trae un personaje del universo Star Wars a tu archivo. Nosotros completamos sus datos.</p>
        <form @submit.prevent="submit">
            <label for="swapi-id">ID del personaje <span class="required">*</span></label>
            <input id="swapi-id" v-model="form.swapi_id" type="number" min="1" max="83" step="1" placeholder="Ej. 1" required autofocus :disabled="form.processing" :aria-invalid="!!form.errors.swapi_id" aria-describedby="swapi-help swapi-error">
            <p id="swapi-help" class="field-help">Del 1 al 83. Prueba con 1 para Luke Skywalker o 5 para Leia Organa.</p>
            <p v-if="form.errors.swapi_id" id="swapi-error" class="field-error" role="alert">{{ form.errors.swapi_id }}</p>
            <div v-if="form.processing" role="status" class="import-progress"><span class="spinner"></span> Consultando SWAPI… puede tardar unos segundos.</div>
            <div class="modal-actions"><button type="button" class="button button-secondary" :disabled="form.processing" @click="close">Cancelar</button><button class="button button-primary" :disabled="form.processing"><AppIcon v-if="!form.processing" name="download" />{{ form.processing ? 'Importando…' : 'Importar personaje' }}</button></div>
        </form>
    </BaseModal>
</template>
