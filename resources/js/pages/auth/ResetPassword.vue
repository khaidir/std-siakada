<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import Button from '@/components/ui/Button.vue';
import Input from '@/components/ui/Input.vue';
import Card from '@/components/ui/Card.vue';

defineProps({
    token: { type: String, required: true },
});

const form = useForm({
    token: '',
    email: '',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <Head title="Atur Ulang Kata Sandi" />
    <div class="flex min-h-screen items-center justify-center p-4">
        <Card class="w-full max-w-md">
            <template #header>
                <h1 class="text-center text-2xl font-semibold text-content">Atur Ulang Kata Sandi</h1>
            </template>
            <form @submit.prevent="submit" class="space-y-4">
                <Input v-model="form.token" type="hidden" />
                <Input v-model="form.email" type="email" label="Email" :error="form.errors.email" required />
                <Input v-model="form.password" type="password" label="Kata Sandi Baru" :error="form.errors.password" required />
                <Input v-model="form.password_confirmation" type="password" label="Ulangi Kata Sandi" required />
                <Button type="submit" variant="primary" class="w-full" :disabled="form.processing">Simpan</Button>
            </form>
        </Card>
    </div>
</template>
