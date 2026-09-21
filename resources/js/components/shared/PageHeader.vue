<script setup>
defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: null },
    breadcrumbs: { type: Array, default: () => [] },
});
</script>

<template>
    <div class="mb-6">
        <nav v-if="breadcrumbs.length" class="mb-2 flex items-center gap-1 text-sm text-muted">
            <span>SIAKAD</span>
            <template v-for="(crumb, i) in breadcrumbs" :key="i">
                <span class="text-slate-300 dark:text-slate-600">/</span>
                <span :class="i === breadcrumbs.length - 1 ? 'font-medium text-content' : ''">{{ crumb }}</span>
            </template>
        </nav>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-content">{{ title }}</h1>
                <p v-if="subtitle" class="mt-1 text-sm text-muted">{{ subtitle }}</p>
            </div>

            <div v-if="$slots.actions" class="flex items-center gap-2">
                <slot name="actions" />
            </div>
        </div>
    </div>
</template>
