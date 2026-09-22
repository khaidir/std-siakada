<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: null },
    // Terima string biasa atau { label, href } agar breadcrumb bisa diklik.
    breadcrumbs: { type: Array, default: () => [] },
});

function labelOf(crumb) {
    return typeof crumb === 'string' ? crumb : crumb.label;
}

function hrefOf(crumb) {
    return typeof crumb === 'string' ? null : crumb.href;
}
</script>

<template>
    <div class="mb-5 sm:mb-6">
        <nav v-if="breadcrumbs.length" class="mb-2 flex flex-wrap items-center gap-1 text-sm text-muted" aria-label="Breadcrumb">
            <span>SIAKAD</span>
            <template v-for="(crumb, i) in breadcrumbs" :key="i">
                <span class="text-border-strong" aria-hidden="true">/</span>
                <Link
                    v-if="hrefOf(crumb) && i !== breadcrumbs.length - 1"
                    :href="hrefOf(crumb)"
                    class="truncate rounded transition hover:text-content focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus"
                >
                    {{ labelOf(crumb) }}
                </Link>
                <span
                    v-else
                    class="truncate"
                    :class="i === breadcrumbs.length - 1 ? 'font-medium text-content' : ''"
                    :aria-current="i === breadcrumbs.length - 1 ? 'page' : undefined"
                >
                    {{ labelOf(crumb) }}
                </span>
            </template>
        </nav>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <h1 class="text-xl font-semibold text-content sm:text-2xl">{{ title }}</h1>
                <p v-if="subtitle" class="mt-1 text-sm text-muted">{{ subtitle }}</p>
            </div>

            <div v-if="$slots.actions" class="flex flex-wrap items-center gap-2">
                <slot name="actions" />
            </div>
        </div>
    </div>
</template>
