<script setup>
defineProps({ name: { type: String, required: true } });
const paths = {
    plus: 'M12 5v14m-7-7h14',
    download: 'M12 3v12m-5-5 5 5 5-5M5 16v4h14v-4',
    search: 'm21 21-5-5M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0',
    arrow: 'M19 12H5m6-6-6 6 6 6',
    edit: 'm16 4 4 4M4 20l4-1L20 7a2.8 2.8 0 0 0-4-4L4 15v5Z',
    trash: 'M3 6h18M9 6V3h6v3M5 6l1 15h12l1-15M10 10v7m4-7v7',
    close: 'm6 6 12 12M6 18 18 6',
    star: 'm12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z',
};
</script>

<template>
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path :d="paths[name] || paths.star" /></svg>
</template>
