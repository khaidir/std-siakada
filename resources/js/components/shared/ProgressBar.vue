<script setup>
import { computed } from 'vue';

const props = defineProps({
    label: { type: String, default: null },
    value: { type: Number, default: 0 },
    max: { type: Number, default: 100 },
    // primary | success | warning | danger | info, atau 'auto' untuk mengikuti ambang batas.
    accent: { type: String, default: 'primary' },
    sublabel: { type: String, default: null },
    showValue: { type: Boolean, default: true },
});

const pct = computed(() => {
    if (!props.max) return 0;
    return Math.min(100, Math.max(0, Math.round((props.value / props.max) * 100)));
});

const bars = {
    primary: 'bg-primary',
    success: 'bg-success',
    warning: 'bg-warning',
    danger: 'bg-danger',
    info: 'bg-info',
};

const tone = computed(() => {
    if (props.accent !== 'auto') return bars[props.accent] ? props.accent : 'primary';
    // Ambang kehadiran: <60% bermasalah, <75% perlu perhatian.
    if (pct.value < 60) return 'danger';
    if (pct.value < 75) return 'warning';
    return 'success';
});
</script>

<template>
    <div>
        <div v-if="label || showValue" class="mb-1.5 flex items-baseline justify-between gap-3">
            <span v-if="label" class="min-w-0 truncate text-sm font-medium text-content">{{ label }}</span>
            <span v-if="showValue" class="shrink-0 text-sm tabular-nums text-muted">{{ pct }}%</span>
        </div>

        <div
            class="h-2 w-full overflow-hidden rounded-full bg-neutral-subtle"
            role="progressbar"
            :aria-valuenow="pct"
            aria-valuemin="0"
            aria-valuemax="100"
            :aria-label="label ?? 'Progres'"
        >
            <div class="h-full rounded-full transition-all duration-500" :class="bars[tone]" :style="{ width: `${pct}%` }"></div>
        </div>

        <p v-if="sublabel" class="mt-1 text-xs text-muted">{{ sublabel }}</p>
    </div>
</template>
