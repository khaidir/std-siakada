<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: { type: String, default: 'neutral' },
    // Tampilkan titik status kecil supaya makna tidak bergantung warna saja.
    dot: { type: Boolean, default: false },
});

const tones = {
    neutral: 'bg-neutral-subtle text-neutral-strong ring-1 ring-inset ring-border',
    primary: 'bg-primary-subtle text-primary-strong ring-1 ring-inset ring-primary/25',
    success: 'bg-success-subtle text-success-strong ring-1 ring-inset ring-success/25',
    warning: 'bg-warning-subtle text-warning-strong ring-1 ring-inset ring-warning/25',
    danger: 'bg-danger-subtle text-danger-strong ring-1 ring-inset ring-danger/25',
    info: 'bg-info-subtle text-info-strong ring-1 ring-inset ring-info/25',
};

const dots = {
    neutral: 'bg-neutral',
    primary: 'bg-primary',
    success: 'bg-success',
    warning: 'bg-warning',
    danger: 'bg-danger',
    info: 'bg-info',
};

/*
 * Nama varian lama masih dipakai di puluhan halaman, jadi dipetakan ke nada semantik
 * alih-alih dihapus. Menghapusnya akan membuat badge kehilangan warna diam-diam.
 */
const aliases = {
    slate: 'neutral',
    gray: 'neutral',
    secondary: 'neutral',
    emerald: 'success',
    green: 'success',
    amber: 'warning',
    yellow: 'warning',
    orange: 'warning',
    rose: 'danger',
    red: 'danger',
    error: 'danger',
    indigo: 'info',
    blue: 'info',
    sky: 'info',
    cyan: 'info',
    purple: 'primary',
    violet: 'primary',
    crimson: 'primary',
};

const tone = computed(() => {
    const key = aliases[props.variant] ?? props.variant;
    return tones[key] ? key : 'neutral';
});
</script>

<template>
    <span
        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium"
        :class="tones[tone]"
    >
        <span v-if="dot" class="h-1.5 w-1.5 shrink-0 rounded-full" :class="dots[tone]" aria-hidden="true"></span>
        <slot />
    </span>
</template>
