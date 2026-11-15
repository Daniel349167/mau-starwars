<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppIcon from '../../Components/AppIcon.vue';
import PersonajeForm from '../../Components/PersonajeForm.vue';
defineProps({ personaje: Object });
</script>

<template>
    <Head :title="`Editar ${personaje.nombre}`" />
    <div class="form-page"><Link href="/personajes" class="back-link"><AppIcon name="arrow" /> Volver al archivo</Link><div class="form-page-heading"><p class="eyebrow">CADA DETALLE CUENTA</p><h1>Editar personaje<span class="heading-dot">.</span></h1><p class="page-intro">Actualiza la información de {{ personaje.nombre }}.</p></div><p v-if="personaje.origen_swapi" class="notice notice-info">Importado desde SWAPI · ID {{ personaje.swapi_id }}. Los cambios se guardan en tu archivo local.</p><PersonajeForm :personaje="personaje" /></div>
</template>
