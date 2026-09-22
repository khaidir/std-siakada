<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import Button from '@/components/ui/Button.vue';
import Input from '@/components/ui/Input.vue';
import Card from '@/components/ui/Card.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <Head title="Daftar" />
    <div class="flex min-h-screen items-center justify-center p-4">
        <Card class="w-full max-w-md">
            <template #header>
                <div class="text-center">
                    <h1 class="text-2xl font-semibold text-content">Daftar Akun</h1>
                </div>
            </template>
            <form @submit.prevent="submit" class="space-y-4">
                <Input v-model="form.name" label="Nama Lengkap" :error="form.errors.name" required />
                <Input v-model="form.email" type="email" label="Email" :error="form.errors.email" required />
                <Input v-model="form.password" type="password" label="Kata Sandi" :error="form.errors.password" required />
                <Input v-model="form.password_confirmation" type="password" label="Ulangi Kata Sandi" required />
                <Button type="submit" variant="primary" class="w-full" :disabled="form.processing">Daftar</Button>
            </form>
        </Card>
    </div>
</template>
