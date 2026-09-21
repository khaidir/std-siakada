<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    message: { type: String, default: null },
    type: { type: String, default: 'success' },
    duration: { type: Number, default: 4000 },
});

const visible = ref(false);

watch(
    () => props.message,
    (msg) => {
        if (!msg) return;
        visible.value = true;
        setTimeout(() => (visible.value = false), props.duration);
    },
);
</script>

<template>
    <Teleport to="body">
        <Transition name="toast">
            <div
                v-if="visible"
                class="fixed right-4 top-4 z-50 rounded-md px-4 py-3 text-sm text-white shadow-lg"
                :class="type === 'error' ? 'bg-rose-600' : 'bg-emerald-600'"
            >
                {{ message }}
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.toast-enter-active,
.toast-leave-active {
    transition: all 0.2s ease;
}
.toast-enter-from,
.toast-leave-to {
    opacity: 0;
    transform: translateY(-8px);
}
</style>
