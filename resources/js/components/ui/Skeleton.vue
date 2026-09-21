<script setup>
defineProps({
    variant: { type: String, default: 'line' },
});

const variants = {
    line: 'bg-slate-200',
    card: 'bg-slate-200 rounded-lg',
};
</script>

<template>
    <div :class="variants[variant]" class="animate-pulse">
        <slot />
    </div>
</template>
