<script setup>
import { Link } from '@inertiajs/vue3';
defineProps({ paginator: { type: Object, required: true } });
</script>

<template>
    <div class="pagination-bar">
        <p class="muted">Mostrando <strong>{{ paginator.from || 0 }}–{{ paginator.to || 0 }}</strong> de <strong>{{ paginator.total }}</strong> personajes</p>
        <nav v-if="paginator.last_page > 1" class="pagination" aria-label="Paginación de personajes">
            <Link v-if="paginator.prev_page_url" :href="paginator.prev_page_url" class="page-button" preserve-scroll aria-label="Página anterior">←</Link><span v-else class="page-button page-disabled" aria-disabled="true">←</span>
            <span class="page-indicator">{{ paginator.current_page }} / {{ paginator.last_page }}</span>
            <Link v-if="paginator.next_page_url" :href="paginator.next_page_url" class="page-button" preserve-scroll aria-label="Página siguiente">→</Link><span v-else class="page-button page-disabled" aria-disabled="true">→</span>
        </nav>
    </div>
</template>
