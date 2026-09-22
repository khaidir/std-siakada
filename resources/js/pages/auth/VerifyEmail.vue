<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';

defineProps({
    status: { type: String, default: null },
});

const form = useForm({});

function resend() {
    form.post(route('verification.send'));
}
</script>

<template>
    <Head title="Verifikasi Email" />
    <div class="flex min-h-screen items-center justify-center p-4">
        <Card class="w-full max-w-md text-center">
            <h1 class="text-2xl font-semibold text-content">Verifikasi Email</h1>
            <p class="mt-2 text-sm text-muted">Silakan periksa email Anda untuk tautan verifikasi.</p>
            <div v-if="status" class="mt-3 rounded-md bg-success-subtle p-3 text-sm text-success-strong">{{ status }}</div>
            <form @submit.prevent="resend" class="mt-4">
                <Button type="submit" variant="primary" :disabled="form.processing">Kirim Ulang</Button>
            </form>
        </Card>
    </div>
</template>
