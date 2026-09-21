<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import StatCard from '@/components/shared/StatCard.vue';
import DataTable from '@/components/shared/DataTable.vue';
import Card from '@/components/ui/Card.vue';
import PageHeader from '@/components/shared/PageHeader.vue';

const props = defineProps({
    stats: { type: Object, required: true },
});

const courses = computed(() => props.stats.courses ?? []);

const columns = [
    { key: 'code', label: 'Kode' },
    { key: 'name', label: 'Mata Kuliah' },
    { key: 'sks', label: 'SKS' },
    { key: 'semester', label: 'Semester' },
    { key: 'type', label: 'Jenis' },
];
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Dashboard Kaprodi" />

        <PageHeader title="Dashboard Kaprodi" subtitle="Ringkasan program studi yang Anda kelola." />

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <StatCard label="Mata Kuliah" :value="stats.total_courses" icon="📚" accent="indigo" />
            <StatCard label="Kelas Ditawarkan" :value="stats.total_offerings" icon="🏫" accent="sky" />
            <StatCard label="Mahasiswa" :value="stats.total_students" icon="🎓" accent="emerald" />
        </div>

        <Card class="mt-6">
            <template #header>
                <h2 class="font-semibold text-content">Daftar Mata Kuliah</h2>
            </template>
            <DataTable :columns="columns" :rows="courses" empty-text="Belum ada mata kuliah." />
        </Card>
    </AuthenticatedLayout>
</template>
