<script setup>
defineProps({
    padded: { type: Boolean, default: true },
    // Garis aksen tipis di sisi atas kartu, mis. 'primary' | 'success' | 'warning' | 'danger' | 'info'.
    accent: { type: String, default: null },
    as: { type: String, default: 'div' },
});

const accents = {
    primary: 'before:bg-primary',
    success: 'before:bg-success',
    warning: 'before:bg-warning',
    danger: 'before:bg-danger',
    info: 'before:bg-info',
};
</script>

<template>
    <component
        :is="as"
        class="relative overflow-hidden rounded-lg border border-border bg-surface shadow-sm"
        :class="accent && accents[accent] ? ['before:absolute before:inset-x-0 before:top-0 before:h-1 before:content-[\'\']', accents[accent]] : ''"
    >
        <div v-if="$slots.header" class="border-b border-border px-4 py-3 sm:px-5 sm:py-4">
            <slot name="header" />
        </div>
        <div :class="padded ? 'p-4 sm:p-5' : ''">
            <slot />
        </div>
        <div v-if="$slots.footer" class="border-t border-border bg-surface-muted px-4 py-3 sm:px-5 sm:py-4">
            <slot name="footer" />
        </div>
    </component>
</template>
