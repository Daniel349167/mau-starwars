<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const flash = computed(() => page.props.flash || {});
</script>

<template>
    <a href="#contenido" class="skip-link">Saltar al contenido</a>
    <header class="site-header">
        <div class="shell header-inner">
            <Link href="/personajes" class="brand" aria-label="Holocron, inicio">
                <img src="/holocron.svg" alt="" width="40" height="40">
                <span>holocron<span class="brand-dot">.</span><small>ARCHIVO GALÁCTICO</small></span>
            </Link>
            <nav aria-label="Navegación principal"><Link href="/personajes" class="nav-link">Personajes</Link></nav>
            <span class="header-note"><span class="status-dot"></span> Una galaxia por descubrir</span>
        </div>
    </header>

    <main id="contenido" class="shell main-content">
        <div v-if="flash.success" :key="page.url + flash.success" role="status" class="notice notice-success">{{ flash.success }}</div>
        <div v-if="flash.warning" role="status" class="notice notice-warning">{{ flash.warning }}</div>
        <slot />
    </main>

    <footer class="shell site-footer"><span>Un pequeño archivo. Una gran galaxia.</span><span>DATOS DE STAR WARS · SWAPI</span></footer>
</template>
