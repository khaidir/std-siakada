<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Dashboard" />
        <h1 class="text-2xl font-semibold text-content">Selamat datang di SIAKAD</h1>
        <p class="mt-2 text-muted">Dashboard role spesifik akan dilengkapi pada prompt selanjutnya.</p>
    </AuthenticatedLayout>
</template>
