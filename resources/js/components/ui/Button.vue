<script setup>
import { computed } from 'vue';

const props = defineProps({
    type: { type: String, default: 'button' },
    variant: { type: String, default: 'primary' },
    size: { type: String, default: 'md' },
    disabled: { type: Boolean, default: false },
    loading: { type: Boolean, default: false },
    block: { type: Boolean, default: false },
});

/*
 * `danger` sengaja dibedakan dari `primary` lewat warna yang lebih gelap DAN lewat aturan
 * pemakaian: aksi destruktif selalu lewat dialog konfirmasi, jadi isian penuh merah gelap
 * hanya muncul di titik yang sudah disengaja pengguna.
 */
const variants = {
    primary: 'bg-primary text-primary-fg hover:bg-primary-hover active:bg-primary-active',
    secondary: 'bg-surface text-content ring-1 ring-inset ring-border-strong hover:bg-surface-muted',
    outline: 'bg-transparent text-primary ring-1 ring-inset ring-primary/40 hover:bg-primary-subtle',
    danger: 'bg-danger text-danger-fg hover:bg-danger-hover',
    ghost: 'bg-transparent text-muted hover:bg-surface-muted hover:text-content',
    success: 'bg-success text-success-fg hover:bg-success-strong',
};

const sizes = {
    sm: 'min-h-8 px-2.5 py-1.5 text-xs',
    md: 'min-h-10 px-4 py-2 text-sm',
    lg: 'min-h-11 px-5 py-2.5 text-base',
};

const tone = computed(() => (variants[props.variant] ? props.variant : 'primary'));
const scale = computed(() => (sizes[props.size] ? props.size : 'md'));
const isDisabled = computed(() => props.disabled || props.loading);
</script>

<template>
    <button
        :type="type"
        :disabled="isDisabled"
        :aria-busy="loading ? 'true' : undefined"
        class="inline-flex items-center justify-center gap-2 rounded-lg font-medium shadow-sm transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus focus-visible:ring-offset-2 focus-visible:ring-offset-surface disabled:cursor-not-allowed disabled:opacity-50"
        :class="[variants[tone], sizes[scale], block ? 'w-full' : '']"
    >
        <svg v-if="loading" class="h-4 w-4 shrink-0 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
        </svg>
        <slot />
    </button>
</template>
