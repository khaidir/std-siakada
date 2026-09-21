<script setup>
import Button from '@/components/ui/Button.vue';
import Modal from '@/components/ui/Modal.vue';

defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, default: 'Konfirmasi' },
    message: { type: String, default: 'Apakah Anda yakin ingin melanjutkan?' },
    confirmLabel: { type: String, default: 'Hapus' },
    cancelLabel: { type: String, default: 'Batal' },
    processing: { type: Boolean, default: false },
    danger: { type: Boolean, default: true },
});

defineEmits(['close', 'confirm']);
</script>

<template>
    <Modal :open="show" :title="title" max-width="max-w-md" @close="$emit('close')">
        <p class="text-sm text-muted">{{ message }}</p>

        <template #footer>
            <Button variant="secondary" :disabled="processing" @click="$emit('close')">
                {{ cancelLabel }}
            </Button>
            <Button
                :variant="danger ? 'danger' : 'primary'"
                :disabled="processing"
                @click="$emit('confirm')"
            >
                {{ processing ? 'Memproses…' : confirmLabel }}
            </Button>
        </template>
    </Modal>
</template>
