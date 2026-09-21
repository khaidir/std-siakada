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

const schedule = computed(() => props.stats.today_schedule ?? []);

const columns = [
    { key: 'course', label: 'Mata Kuliah' },
    { key: 'classroom', label: 'Ruangan' },
    { key: 'start_time', label: 'Mulai' },
    { key: 'end_time', label: 'Selesai' },
];
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Dashboard Mahasiswa" />

        <PageHeader title="Dashboard Mahasiswa" subtitle="Ringkasan studi Anda semester ini." />

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <StatCard label="Total SKS" :value="stats.total_sks" icon="📚" accent="indigo" />
            <StatCard label="Kelas Diikuti" :value="stats.total_classes" icon="🏫" accent="sky" />
            <StatCard label="Rata-rata Nilai" :value="stats.average_grade ?? '—'" icon="📊" accent="emerald" />
        </div>

        <Card class="mt-6">
            <template #header>
                <h2 class="font-semibold text-content">Jadwal Kuliah Hari Ini</h2>
            </template>
            <DataTable :columns="columns" :rows="schedule" empty-text="Tidak ada jadwal hari ini." />
        </Card>
    </AuthenticatedLayout>
</template>
