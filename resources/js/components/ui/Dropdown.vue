<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';

defineProps({
    align: { type: String, default: 'right' },
});

const open = ref(false);
const root = ref(null);

function handleClickOutside(event) {
    if (root.value && !root.value.contains(event.target)) {
        open.value = false;
    }
}

onMounted(() => document.addEventListener('click', handleClickOutside));
onBeforeUnmount(() => document.removeEventListener('click', handleClickOutside));
</script>

<template>
    <div ref="root" class="relative">
        <div @click="open = !open">
            <slot name="trigger" />
        </div>
        <Transition name="dropdown">
            <div
                v-if="open"
                class="absolute z-40 mt-2 min-w-[10rem] overflow-hidden rounded-md border border-border bg-surface py-1 shadow-lg"
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
