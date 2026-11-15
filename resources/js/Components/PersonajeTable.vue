<script setup>
import { Link } from '@inertiajs/vue3';
import AppIcon from './AppIcon.vue';

defineProps({ personajes: { type: Array, required: true } });
defineEmits(['delete']);
const generoLabel = { masculino: 'Masculino', femenino: 'Femenino', 'n/a': 'No aplica' };
const initials = (name) => name.split(/\s+/).slice(0, 2).map((part) => part[0]).join('').toUpperCase();
</script>

<template>
    <table class="characters-table">
        <caption class="sr-only">Personajes registrados y acciones disponibles</caption>
        <thead><tr><th scope="col">Personaje</th><th scope="col">Altura</th><th scope="col">Masa</th><th scope="col">Género</th><th scope="col">Origen</th><th scope="col" class="text-right">Acciones</th></tr></thead>
        <tbody><tr v-for="personaje in personajes" :key="personaje.id">
            <td class="character-cell"><div class="character-identity"><span class="avatar" :class="{ 'avatar-imported': personaje.origen_swapi }">{{ initials(personaje.nombre) }}</span><div><span class="character-name">{{ personaje.nombre }}</span><small>{{ personaje.planeta_origen || 'Planeta sin especificar' }}</small></div></div></td>
            <td data-label="Altura">{{ personaje.altura === null ? '—' : `${personaje.altura} cm` }}</td>
            <td data-label="Masa">{{ personaje.masa === null ? '—' : `${personaje.masa} kg` }}</td>
            <td data-label="Género">{{ generoLabel[personaje.genero] }}</td>
            <td data-label="Origen"><span class="badge" :class="personaje.origen_swapi ? 'badge-swapi' : 'badge-manual'"><span class="badge-dot"></span>{{ personaje.origen_swapi ? 'SWAPI' : 'Manual' }}</span></td>
            <td class="row-actions"><Link :href="`/personajes/${personaje.id}/editar`" class="icon-button" :aria-label="`Editar a ${personaje.nombre}`" title="Editar"><AppIcon name="edit" /></Link><button class="icon-button delete-button" :aria-label="`Eliminar a ${personaje.nombre}`" title="Eliminar" @click="$emit('delete', personaje)"><AppIcon name="trash" /></button></td>
        </tr></tbody>
    </table>
</template>
