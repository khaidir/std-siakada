<script setup>
defineProps({
    title: { type: String, default: 'Belum ada data' },
    description: { type: String, default: 'Belum ada data untuk ditampilkan.' },
});
</script>

<template>
    <div class="flex flex-col items-center justify-center px-6 py-12 text-center">
        <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-2xl">
            <slot name="icon">📭</slot>
        </div>
        <h3 class="text-base font-medium text-slate-900">{{ title }}</h3>
        <p class="mt-1 text-sm text-slate-500">{{ description }}</p>
        <div v-if="$slots.default" class="mt-4">
            <slot />
        </div>
    </div>
</template>
