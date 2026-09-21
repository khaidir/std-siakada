<script setup>
defineProps({
    open: { type: Boolean, default: false },
    title: { type: String, default: '' },
    maxWidth: { type: String, default: 'max-w-lg' },
});

defineEmits(['close']);
</script>

<template>
    <Teleport to="body">
        <Transition name="fade">
            <div
                v-if="open"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
            >
                <div
                    class="absolute inset-0 bg-slate-900/50"
                    @click="$emit('close')"
                />
                <div
                    class="relative z-10 w-full rounded-lg bg-white shadow-xl"
                    :class="maxWidth"
                >
                    <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                        <h3 class="text-lg font-semibold text-slate-900">{{ title }}</h3>
                        <button
                            type="button"
                            class="rounded-md p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600"
                            @click="$emit('close')"
                        >
                            ✕
                        </button>
                    </div>
                    <div class="px-5 py-4">
                        <slot />
                    </div>
                    <div v-if="$slots.footer" class="flex justify-end gap-2 border-t border-slate-200 px-5 py-4">
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
