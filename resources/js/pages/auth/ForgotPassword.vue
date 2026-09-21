<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import Button from '@/components/ui/Button.vue';
import Input from '@/components/ui/Input.vue';
import Card from '@/components/ui/Card.vue';

defineProps({
    status: { type: String, default: null },
});

const form = useForm({ email: '' });

function submit() {
    form.post(route('password.email'));
}
</script>

<template>
    <Head title="Lupa Kata Sandi" />
    <div class="flex min-h-screen items-center justify-center p-4">
        <Card class="w-full max-w-md">
            <template #header>
                <div class="text-center">
                    <h1 class="text-2xl font-semibold text-slate-900">Lupa Kata Sandi</h1>
                    <p class="mt-1 text-sm text-slate-500">Masukkan email untuk menerima tautan reset.</p>
                </div>
            </template>
            <form @submit.prevent="submit" class="space-y-4">
                <div v-if="status" class="rounded-md bg-emerald-50 p-3 text-sm text-emerald-700">{{ status }}</div>
                <Input v-model="form.email" type="email" label="Email" :error="form.errors.email" required />
                <Button type="submit" variant="primary" class="w-full" :disabled="form.processing">Kirim Tautan</Button>
            </form>
        </Card>
    </div>
</template>
