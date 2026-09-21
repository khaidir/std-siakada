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

const distribution = computed(() => props.stats.grade_distribution ?? []);
const trend = computed(() => props.stats.gpa_trend ?? []);

const distributionColumns = [
    { key: 'letter', label: 'Nilai' },
    { key: 'total', label: 'Jumlah' },
];

const trendColumns = [
    { key: 'year', label: 'Angkatan' },
    { key: 'average', label: 'Rata-rata IPK' },
];
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Dashboard Pimpinan" />

        <PageHeader title="Dashboard Pimpinan" subtitle="Indikator akademik tingkat institusi." />

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <StatCard label="Total Mahasiswa" :value="stats.total_students" icon="🎓" accent="indigo" />
            <StatCard label="Rata-rata IPK" :value="stats.average_gpa" icon="📈" accent="emerald" />
            <StatCard label="Total Dosen" :value="stats.total_lecturers" icon="🧑‍🏫" accent="sky" />
            <StatCard label="Program Studi" :value="stats.total_programs" icon="🏛️" accent="amber" />
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <Card>
                <template #header>
                    <h2 class="font-semibold text-content">Distribusi Nilai</h2>
                </template>
                <DataTable :columns="distributionColumns" :rows="distribution" empty-text="Belum ada data nilai." />
            </Card>

            <Card>
                <template #header>
                    <h2 class="font-semibold text-content">Tren IPK per Angkatan</h2>
                </template>
                <DataTable :columns="trendColumns" :rows="trend" empty-text="Belum ada data angkatan." />
            </Card>
        </div>
    </AuthenticatedLayout>
</template>
