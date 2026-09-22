<script setup>
import { computed, onBeforeUnmount, ref, watch } from 'vue';

const props = defineProps({
    message: { type: String, default: null },
    type: { type: String, default: 'success' },
    duration: { type: Number, default: 4000 },
});

const visible = ref(false);
let timer = null;

const tones = {
    success: 'bg-success-subtle text-success-strong ring-success/30',
    error: 'bg-danger-subtle text-danger-strong ring-danger/30',
    warning: 'bg-warning-subtle text-warning-strong ring-warning/30',
    info: 'bg-info-subtle text-info-strong ring-info/30',
};

const icons = {
    success: 'M5 13l4 4L19 7',
    error: 'M6 6l12 12M18 6L6 18',
    warning: 'M12 9v4m0 4h.01M10.3 3.9L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z',
    info: 'M12 16v-4m0-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
};

const tone = computed(() => (tones[props.type] ? props.type : 'info'));

function clear() {
    if (timer) {
        clearTimeout(timer);
        timer = null;
    }
}

function dismiss() {
    clear();
    visible.value = false;
}

watch(
    () => props.message,
    (msg) => {
        // Timer lama wajib dibersihkan; kalau tidak, pesan baru ikut tertutup
        // oleh hitungan mundur pesan sebelumnya.
        clear();
        if (!msg) {
            visible.value = false;
            return;
        }
        visible.value = true;
        timer = setTimeout(() => (visible.value = false), props.duration);
    },
);

onBeforeUnmount(clear);
</script>

<template>
    <Teleport to="body">
        <Transition name="toast">
            <div
                v-if="visible"
                :role="type === 'error' ? 'alert' : 'status'"
                :aria-live="type === 'error' ? 'assertive' : 'polite'"
                class="fixed inset-x-4 top-4 z-50 flex items-start gap-3 rounded-lg px-4 py-3 text-sm shadow-lg ring-1 ring-inset sm:inset-x-auto sm:right-4 sm:max-w-sm"
                :class="tones[tone]"
            >
                <svg class="mt-0.5 h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" :d="icons[tone]" />
                </svg>

                <span class="flex-1">{{ message }}</span>

                <button
                    type="button"
                    class="shrink-0 rounded p-0.5 opacity-70 transition hover:opacity-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus"
                    aria-label="Tutup notifikasi"
                    @click="dismiss"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" d="M6 6l12 12M18 6L6 18" />
                    </svg>
                </button>
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
