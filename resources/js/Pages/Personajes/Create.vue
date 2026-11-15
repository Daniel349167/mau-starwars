<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppIcon from '../../Components/AppIcon.vue';
import PersonajeForm from '../../Components/PersonajeForm.vue';
</script>

<template>
    <Head title="Crear personaje" />
    <div class="form-page"><Link href="/personajes" class="back-link"><AppIcon name="arrow" /> Volver al archivo</Link><div class="form-page-heading"><p class="eyebrow">AMPLÍA TU UNIVERSO</p><h1>Un nuevo personaje<span class="heading-dot">.</span></h1><p class="page-intro">Añade una nueva historia a tu colección.</p></div><PersonajeForm /></div>
</template>
