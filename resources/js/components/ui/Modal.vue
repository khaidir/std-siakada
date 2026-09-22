<script setup>
import { nextTick, onBeforeUnmount, ref, useId, watch } from 'vue';

const props = defineProps({
    open: { type: Boolean, default: false },
    title: { type: String, default: '' },
    maxWidth: { type: String, default: 'max-w-lg' },
});

const emit = defineEmits(['close']);

const panel = ref(null);
const titleId = `modal-title-${useId()}`;
let lastFocused = null;

function onKeydown(event) {
    if (event.key === 'Escape') {
        emit('close');
        return;
    }

    if (event.key !== 'Tab' || !panel.value) return;

    // Focus trap sederhana: Tab berputar di dalam dialog.
    const focusable = panel.value.querySelectorAll(
        'a[href], button:not([disabled]), textarea, input, select, [tabindex]:not([tabindex="-1"])',
    );
    if (!focusable.length) return;

    const first = focusable[0];
    const last = focusable[focusable.length - 1];

    if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
    }
}

function lock() {
    lastFocused = document.activeElement;
    document.body.style.overflow = 'hidden';
    document.addEventListener('keydown', onKeydown);
    nextTick(() => panel.value?.focus());
}

function unlock() {
    document.body.style.overflow = '';
    document.removeEventListener('keydown', onKeydown);
    lastFocused?.focus?.();
    lastFocused = null;
}

watch(() => props.open, (value) => (value ? lock() : unlock()));

onBeforeUnmount(() => {
    document.body.style.overflow = '';
    document.removeEventListener('keydown', onKeydown);
});
</script>

<template>
    <Teleport to="body">
        <Transition name="fade">
            <div v-if="open" class="fixed inset-0 z-50 flex items-end justify-center p-0 sm:items-center sm:p-4">
                <div class="absolute inset-0 bg-black/50" @click="emit('close')"></div>

                <div
                    ref="panel"
                    tabindex="-1"
                    role="dialog"
                    aria-modal="true"
                    :aria-labelledby="title ? titleId : undefined"
                    class="relative z-10 flex max-h-[90dvh] w-full flex-col rounded-t-2xl bg-surface shadow-xl focus:outline-none sm:rounded-lg"
                    :class="maxWidth"
                >
                    <div class="flex shrink-0 items-center justify-between gap-3 border-b border-border px-5 py-4">
                        <h3 :id="titleId" class="text-lg font-semibold text-content">{{ title }}</h3>
                        <button
                            type="button"
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-muted transition hover:bg-surface-muted hover:text-content focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus"
                            aria-label="Tutup"
                            @click="emit('close')"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" d="M6 6l12 12M18 6L6 18" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto px-5 py-4">
                        <slot />
                    </div>

                    <div v-if="$slots.footer" class="flex shrink-0 flex-col-reverse gap-2 border-t border-border px-5 py-4 sm:flex-row sm:justify-end">
                        <slot name="footer" />
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.15s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
