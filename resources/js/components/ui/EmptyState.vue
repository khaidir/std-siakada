<script setup>
defineProps({
    title: { type: String, default: 'Belum ada data' },
    description: { type: String, default: 'Belum ada data untuk ditampilkan.' },
});
</script>

<template>
    <div class="flex flex-col items-center justify-center px-6 py-12 text-center">
        <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-neutral-subtle text-2xl">
            <slot name="icon">📭</slot>
        </div>
        <h3 class="text-base font-medium text-content">{{ title }}</h3>
        <p class="mt-1 max-w-sm text-sm text-muted">{{ description }}</p>
        <div v-if="$slots.default" class="mt-4">
            <slot />
        </div>
    </div>
</template>
