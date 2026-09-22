<script setup>
import { nextTick, ref, watch } from 'vue';
import Button from '@/components/ui/Button.vue';
import Modal from '@/components/ui/Modal.vue';

const props = defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, default: 'Konfirmasi' },
    message: { type: String, default: 'Apakah Anda yakin ingin melanjutkan?' },
    confirmLabel: { type: String, default: 'Hapus' },
    cancelLabel: { type: String, default: 'Batal' },
    processing: { type: Boolean, default: false },
    danger: { type: Boolean, default: true },
});

defineEmits(['close', 'confirm']);

const cancelButton = ref(null);

// Fokus awal jatuh ke Batal, bukan ke tombol destruktif.
watch(
    () => props.show,
    (open) => {
        if (open) nextTick(() => cancelButton.value?.$el?.focus?.());
    },
);
</script>

<template>
    <Modal :open="show" :title="title" max-width="max-w-md" @close="$emit('close')">
        <div class="flex items-start gap-3">
            <span
                v-if="danger"
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-danger-subtle text-danger-strong"
                aria-hidden="true"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 9v4m0 4h.01M10.3 3.9L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"
                    />
                </svg>
            </span>
            <p class="text-sm text-muted">{{ message }}</p>
        </div>

        <template #footer>
            <Button ref="cancelButton" variant="secondary" :disabled="processing" @click="$emit('close')">
                {{ cancelLabel }}
            </Button>
            <Button :variant="danger ? 'danger' : 'primary'" :loading="processing" @click="$emit('confirm')">
                {{ processing ? 'Memproses…' : confirmLabel }}
            </Button>
        </template>
    </Modal>
</template>
