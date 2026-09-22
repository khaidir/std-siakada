<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: { type: String, default: 'line' },
});

const variants = {
    line: 'rounded bg-neutral-subtle',
    text: 'rounded h-4 bg-neutral-subtle',
    card: 'rounded-lg bg-neutral-subtle',
    circle: 'rounded-full bg-neutral-subtle',
};

const tone = computed(() => variants[props.variant] ?? variants.line);
</script>

<template>
    <div class="animate-pulse" :class="tone" aria-hidden="true">
        <slot />
    </div>
</template>
