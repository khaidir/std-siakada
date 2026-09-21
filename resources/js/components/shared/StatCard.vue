<script setup>
defineProps({
    label: { type: String, required: true },
    value: { type: [String, Number], default: 0 },
    sublabel: { type: String, default: null },
    icon: { type: String, default: '📊' },
    accent: { type: String, default: 'indigo' },
});

const accents = {
    indigo: 'bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-300',
    emerald: 'bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-300',
    amber: 'bg-amber-100 text-amber-600 dark:bg-amber-500/20 dark:text-amber-300',
    rose: 'bg-rose-100 text-rose-600 dark:bg-rose-500/20 dark:text-rose-300',
    sky: 'bg-sky-100 text-sky-600 dark:bg-sky-500/20 dark:text-sky-300',
};
</script>

<template>
    <div class="rounded-lg border border-border bg-surface p-5 shadow-sm">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="truncate text-sm font-medium text-muted">{{ label }}</p>
                <p class="mt-1 text-2xl font-semibold text-content">{{ value }}</p>
                <p v-if="sublabel" class="mt-1 text-xs text-muted">{{ sublabel }}</p>
            </div>
            <span
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-lg"
                :class="accents[accent]"
            >
                {{ icon }}
            </span>
        </div>
    </div>
</template>
