<script setup>
defineProps({
    type: { type: String, default: 'button' },
    variant: { type: String, default: 'primary' },
    size: { type: String, default: 'md' },
    disabled: { type: Boolean, default: false },
});

const variants = {
    primary: 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500',
    secondary: 'bg-surface text-content border border-border hover:bg-surface-muted focus:ring-slate-400',
    danger: 'bg-rose-600 text-white hover:bg-rose-700 focus:ring-rose-500',
    ghost: 'bg-transparent text-muted hover:bg-surface-muted focus:ring-slate-400',
    success: 'bg-emerald-600 text-white hover:bg-emerald-700 focus:ring-emerald-500',
};

const sizes = {
    sm: 'px-2.5 py-1.5 text-xs',
    md: 'px-4 py-2 text-sm',
    lg: 'px-5 py-2.5 text-base',
};
</script>

<template>
    <button
        :type="type"
        :disabled="disabled"
        class="inline-flex items-center justify-center gap-2 rounded-md font-medium shadow-sm transition focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
        :class="[variants[variant], sizes[size]]"
    >
        <slot />
    </button>
</template>
