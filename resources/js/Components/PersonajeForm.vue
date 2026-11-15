<script setup>
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({ personaje: { type: Object, default: null } });
const form = useForm({
    nombre: props.personaje?.nombre ?? '',
    altura: props.personaje?.altura ?? '',
    masa: props.personaje?.masa ?? '',
    color_cabello: props.personaje?.color_cabello ?? '',
    color_ojos: props.personaje?.color_ojos ?? '',
    genero: props.personaje?.genero ?? '',
    planeta_origen: props.personaje?.planeta_origen ?? '',
});

function submit() {
    if (props.personaje) form.put(`/personajes/${props.personaje.id}`);
    else form.post('/personajes');
}
</script>

<template>
    <form class="panel character-form" @submit.prevent="submit">
        <div class="form-section-heading"><span class="section-number">01</span><div><h2>Identidad del personaje</h2><p class="muted">Los campos con <span class="required">*</span> son obligatorios.</p></div></div>
        <fieldset :disabled="form.processing" class="form-grid">
            <div class="field full-width"><label for="nombre">Nombre <span class="required">*</span></label><input id="nombre" v-model="form.nombre" required minlength="3" maxlength="100" placeholder="Ej. Ahsoka Tano" :aria-invalid="!!form.errors.nombre" aria-describedby="error-nombre"><p v-if="form.errors.nombre" id="error-nombre" class="field-error">{{ form.errors.nombre }}</p></div>
            <div class="field"><label for="genero">Género <span class="required">*</span></label><select id="genero" v-model="form.genero" required :aria-invalid="!!form.errors.genero" aria-describedby="error-genero"><option disabled value="">Seleccionar género</option><option value="masculino">Masculino</option><option value="femenino">Femenino</option><option value="n/a">No aplica</option></select><p v-if="form.errors.genero" id="error-genero" class="field-error">{{ form.errors.genero }}</p></div>
            <div class="field"><label for="planeta_origen">Planeta de origen</label><input id="planeta_origen" v-model="form.planeta_origen" maxlength="100" placeholder="Ej. Coruscant" :aria-invalid="!!form.errors.planeta_origen" aria-describedby="error-planeta"><p v-if="form.errors.planeta_origen" id="error-planeta" class="field-error">{{ form.errors.planeta_origen }}</p></div>
            <div class="form-section-heading full-width secondary-section"><span class="section-number">02</span><div><h2>Características</h2><p class="muted">Completa lo que sepas; puedes dejar estos campos vacíos.</p></div></div>
            <div class="field"><label for="altura">Altura <span class="label-unit">cm</span></label><input id="altura" v-model="form.altura" type="number" min="1" max="300" step="1" placeholder="Ej. 170" :aria-invalid="!!form.errors.altura" aria-describedby="error-altura"><p v-if="form.errors.altura" id="error-altura" class="field-error">{{ form.errors.altura }}</p></div>
            <div class="field"><label for="masa">Masa <span class="label-unit">kg</span></label><input id="masa" v-model="form.masa" type="number" min="1" max="500" step="1" placeholder="Ej. 65" :aria-invalid="!!form.errors.masa" aria-describedby="error-masa"><p v-if="form.errors.masa" id="error-masa" class="field-error">{{ form.errors.masa }}</p></div>
            <div class="field"><label for="color_cabello">Color de cabello</label><input id="color_cabello" v-model="form.color_cabello" maxlength="100" placeholder="Ej. Castaño" :aria-invalid="!!form.errors.color_cabello" aria-describedby="error-cabello"><p v-if="form.errors.color_cabello" id="error-cabello" class="field-error">{{ form.errors.color_cabello }}</p></div>
            <div class="field"><label for="color_ojos">Color de ojos</label><input id="color_ojos" v-model="form.color_ojos" maxlength="100" placeholder="Ej. Marrón" :aria-invalid="!!form.errors.color_ojos" aria-describedby="error-ojos"><p v-if="form.errors.color_ojos" id="error-ojos" class="field-error">{{ form.errors.color_ojos }}</p></div>
        </fieldset>
        <p v-if="form.hasErrors" role="alert" class="field-error form-error-summary">Revisa los campos indicados antes de guardar.</p>
        <div class="form-actions"><Link href="/personajes" class="button button-secondary" :class="{ 'disabled-link': form.processing }" :aria-disabled="form.processing" :tabindex="form.processing ? -1 : 0">Cancelar</Link><button type="submit" class="button button-primary" :disabled="form.processing"><span v-if="form.processing" class="spinner"></span>{{ form.processing ? 'Guardando…' : personaje ? 'Actualizar personaje' : 'Guardar personaje' }}</button></div>
    </form>
</template>
