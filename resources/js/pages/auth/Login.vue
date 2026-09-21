<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import Button from '@/components/ui/Button.vue';
import Input from '@/components/ui/Input.vue';
import Card from '@/components/ui/Card.vue';

defineProps({
    status: {
        type: String,
        default: null,
    },
});

const page = usePage();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <Head title="Masuk" />

    <div class="flex min-h-screen items-center justify-center p-4">
        <Card class="w-full max-w-md">
            <template #header>
                <div class="text-center">
                    <h1 class="text-2xl font-semibold text-slate-900">SIAKAD</h1>
                    <p class="mt-1 text-sm text-slate-500">Sistem Informasi Akademik</p>
                </div>
            </template>

            <form @submit.prevent="submit" class="space-y-4">
                <div v-if="status" class="rounded-md bg-emerald-50 p-3 text-sm text-emerald-700">
                    {{ status }}
                </div>

                <div
                    v-if="page.props.flash?.error"
                    class="rounded-md bg-rose-50 p-3 text-sm text-rose-700"
                >
                    {{ page.props.flash.error }}
                </div>

                <Input
                    v-model="form.email"
                    type="email"
                    label="Email"
                    placeholder="nama@kampus.ac.id"
                    :error="form.errors.email"
                    required
                    autofocus
                />

                <Input
                    v-model="form.password"
                    type="password"
                    label="Kata Sandi"
                    placeholder="••••••••"
                    :error="form.errors.password"
                    required
                />

                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input v-model="form.remember" type="checkbox" class="rounded border-slate-300" />
                    Ingat saya
                </label>

                <Button type="submit" variant="primary" class="w-full" :disabled="form.processing">
                    Masuk
                </Button>
            </form>
        </Card>
    </div>
</template>
