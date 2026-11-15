<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AppIcon from '../../Components/AppIcon.vue';
import PersonajeTable from '../../Components/PersonajeTable.vue';
import Pagination from '../../Components/Pagination.vue';
import ImportModal from '../../Components/ImportModal.vue';
import DeleteModal from '../../Components/DeleteModal.vue';

const props = defineProps({ personajes: Object, resumen: Object, filtros: Object });
const importing = ref(false);
const deleting = ref(null);
const filters = useForm({ buscar: props.filtros.buscar, genero: props.filtros.genero });

watch(() => props.filtros, (value) => {
    filters.buscar = value.buscar;
    filters.genero = value.genero;
});

function search() {
    filters.get('/personajes', { preserveState: true, preserveScroll: true, replace: true });
}
</script>

<template>
    <Head title="Personajes" />
    <section class="page-heading"><div><p class="eyebrow">EXPLORA · REGISTRA · DESCUBRE</p><h1>Personajes de otra galaxia<span class="heading-dot">.</span></h1><p class="page-intro">Tu colección del universo Star Wars, en un solo lugar.</p></div><div class="heading-actions"><button class="button button-secondary" @click="importing = true"><AppIcon name="download" /> Importar desde SWAPI</button><Link href="/personajes/crear" class="button button-primary"><AppIcon name="plus" /> Crear nuevo</Link></div></section>

    <section class="overview" aria-label="Resumen del archivo">
        <div class="stats-panel"><div class="stat"><span class="stat-label">PERSONAJES REGISTRADOS</span><strong>{{ String(resumen.total).padStart(2, '0') }}</strong><span class="muted">Historias por descubrir</span></div><div class="stat"><span class="stat-label">IMPORTADOS DE SWAPI</span><strong>{{ String(resumen.swapi).padStart(2, '0') }}</strong><span class="muted">Directo desde la galaxia</span></div><div class="stat"><span class="stat-label">CREADOS POR TI</span><strong>{{ String(resumen.total - resumen.swapi).padStart(2, '0') }}</strong><span class="muted">Tu aporte al archivo</span></div></div>
        <aside class="galaxy-note"><div class="orbit orbit-one"></div><div class="orbit orbit-two"></div><AppIcon name="star" /><p>Siempre hay alguien<br>más por descubrir.</p><span>EL ARCHIVO SIGUE CRECIENDO</span></aside>
    </section>

    <section class="panel collection-panel" aria-labelledby="collection-title">
        <div class="collection-heading"><h2 id="collection-title">Tu archivo <span class="count-pill">{{ personajes.total }}</span></h2><span class="muted collection-subtitle">Cada personaje tiene una historia.</span></div>
        <form class="filter-bar" role="search" @submit.prevent="search"><div class="search-field"><AppIcon name="search" /><label for="buscar" class="sr-only">Buscar por nombre</label><input id="buscar" v-model="filters.buscar" type="search" maxlength="100" placeholder="Buscar por nombre…"></div><label for="filtro-genero" class="sr-only">Filtrar por género</label><select id="filtro-genero" v-model="filters.genero"><option value="">Todos los géneros</option><option value="masculino">Masculino</option><option value="femenino">Femenino</option><option value="n/a">No aplica</option></select><button class="button button-secondary" :disabled="filters.processing">{{ filters.processing ? 'Buscando…' : 'Buscar' }}</button><Link v-if="filtros.buscar || filtros.genero" href="/personajes" class="clear-filters">Limpiar</Link></form>
        <p v-if="filters.hasErrors" role="alert" class="field-error filter-errors">{{ Object.values(filters.errors).join(' ') }}</p>
        <PersonajeTable v-if="personajes.data.length" :personajes="personajes.data" @delete="deleting = $event" />
        <div v-else class="empty-state"><span class="empty-icon"><AppIcon name="star" /></span><h3>{{ resumen.total ? 'No encontramos coincidencias' : 'Tu galaxia empieza aquí' }}</h3><p class="muted">{{ resumen.total ? 'Prueba con otro nombre o cambia el filtro de género.' : 'Crea tu primer personaje o importa uno desde SWAPI.' }}</p><button v-if="!resumen.total" class="button button-primary" @click="importing = true"><AppIcon name="download" /> Importar un personaje</button></div>
        <Pagination :paginator="personajes" />
    </section>
    <ImportModal :open="importing" @close="importing = false" />
    <DeleteModal :personaje="deleting" @close="deleting = null" />
</template>
