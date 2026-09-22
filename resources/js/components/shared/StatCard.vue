<script setup>
import { computed } from 'vue';

const props = defineProps({
    label: { type: String, required: true },
    value: { type: [String, Number], default: 0 },
    sublabel: { type: String, default: null },
    icon: { type: String, default: '📊' },
    accent: { type: String, default: 'primary' },
    // { value: '+2,1%', direction: 'up' | 'down' | 'flat' }
    trend: { type: Object, default: null },
});

const accents = {
    primary: 'bg-primary-subtle text-primary-strong',
    success: 'bg-success-subtle text-success-strong',
    warning: 'bg-warning-subtle text-warning-strong',
    danger: 'bg-danger-subtle text-danger-strong',
    info: 'bg-info-subtle text-info-strong',
};

// Nilai accent lama tetap jalan supaya halaman yang sudah ada tidak kehilangan warna.
const aliases = {
    indigo: 'info',
    blue: 'info',
    sky: 'info',
    emerald: 'success',
    green: 'success',
    amber: 'warning',
    yellow: 'warning',
    rose: 'danger',
    red: 'danger',
    slate: 'primary',
    gray: 'primary',
};

const tone = computed(() => {
    const key = aliases[props.accent] ?? props.accent;
    return accents[key] ? key : 'primary';
});

const trendTone = computed(() => {
    if (props.trend?.direction === 'up') return 'text-success-strong';
    if (props.trend?.direction === 'down') return 'text-danger-strong';
    return 'text-muted';
});

const trendPath = computed(() => {
    if (props.trend?.direction === 'up') return 'M12 19V5M5 12l7-7 7 7';
    if (props.trend?.direction === 'down') return 'M12 5v14M19 12l-7 7-7-7';
    return 'M5 12h14';
});
</script>

<template>
    <div class="rounded-lg border border-border bg-surface p-4 shadow-sm sm:p-5">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="truncate text-sm font-medium text-muted">{{ label }}</p>
                <p class="mt-1 text-2xl font-semibold tabular-nums text-content">{{ value }}</p>

                <p v-if="sublabel" class="mt-1 text-xs text-muted">{{ sublabel }}</p>

                <!-- Arah tren ditandai ikon panah, bukan warna saja. -->
                <p v-if="trend" class="mt-1.5 inline-flex items-center gap-1 text-xs font-medium" :class="trendTone">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" :d="trendPath" />
                    </svg>
                    {{ trend.value }}
                </p>
            </div>

            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-lg" :class="accents[tone]" aria-hidden="true">
                {{ icon }}
            </span>
        </div>
    </div>
</template>
