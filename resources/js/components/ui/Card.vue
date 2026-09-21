<script setup>
defineProps({
    padded: { type: Boolean, default: true },
});
</script>

<template>
    <div class="overflow-hidden rounded-lg border border-border bg-surface shadow-sm">
        <div v-if="$slots.header" class="border-b border-border px-5 py-4">
            <slot name="header" />
        </div>
        <div :class="padded ? 'p-5' : ''">
            <slot />
        </div>
        <div v-if="$slots.footer" class="border-t border-border bg-surface-muted px-5 py-4">
            <slot name="footer" />
        </div>
    </div>
</template>
