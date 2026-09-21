<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Dashboard" />
        <h1 class="text-2xl font-semibold text-slate-900">Selamat datang di SIAKAD</h1>
        <p class="mt-2 text-slate-500">Dashboard role spesifik akan dilengkapi pada prompt selanjutnya.</p>
    </AuthenticatedLayout>
</template>
