<script setup>
import { nextTick, onBeforeUnmount, onMounted, ref } from 'vue';

defineProps({
    align: { type: String, default: 'right' },
});

const open = ref(false);
const root = ref(null);
const panel = ref(null);

function close(refocus = false) {
    open.value = false;
    if (refocus) {
        root.value?.querySelector('[data-dropdown-trigger] button, [data-dropdown-trigger]')?.focus?.();
    }
}

function toggle() {
    open.value = !open.value;
    if (open.value) {
        nextTick(() => {
            panel.value?.querySelector('a, button, [tabindex]:not([tabindex="-1"])')?.focus?.();
        });
    }
}

function handleClickOutside(event) {
    if (root.value && !root.value.contains(event.target)) {
        open.value = false;
    }
}

function handleKeydown(event) {
    if (!open.value) return;

    if (event.key === 'Escape') {
        event.preventDefault();
        close(true);
        return;
    }

    if (event.key !== 'ArrowDown' && event.key !== 'ArrowUp') return;

    const items = Array.from(panel.value?.querySelectorAll('a, button, [tabindex]:not([tabindex="-1"])') ?? []);
    if (!items.length) return;

    event.preventDefault();
    const current = items.indexOf(document.activeElement);
    const step = event.key === 'ArrowDown' ? 1 : -1;
    items[(current + step + items.length) % items.length].focus();
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    document.addEventListener('keydown', handleKeydown);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
    document.removeEventListener('keydown', handleKeydown);
});
</script>

<template>
    <div ref="root" class="relative">
        <div data-dropdown-trigger :aria-expanded="open" aria-haspopup="menu" @click="toggle">
            <slot name="trigger" />
        </div>

        <Transition name="dropdown">
            <div
                v-if="open"
                ref="panel"
                role="menu"
                class="absolute z-40 mt-2 max-h-[70vh] w-max min-w-40 max-w-[calc(100vw-2rem)] overflow-y-auto overflow-x-hidden rounded-lg border border-border bg-surface py-1 shadow-lg"
                :class="align === 'right' ? 'right-0' : 'left-0'"
                @click="open = false"
            >
                <slot />
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
    transition: opacity 0.1s ease, transform 0.1s ease;
}
.dropdown-enter-from,
.dropdown-leave-to {
    opacity: 0;
    transform: scale(0.97);
}
</style>
