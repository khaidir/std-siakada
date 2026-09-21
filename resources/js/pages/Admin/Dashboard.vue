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

const activities = computed(() => props.stats.recent_activities ?? []);

const columns = [
    { key: 'user', label: 'Pengguna' },
    { key: 'action', label: 'Aksi' },
    { key: 'model_type', label: 'Entitas' },
    { key: 'created_at', label: 'Waktu' },
];
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Dashboard Admin" />

        <PageHeader title="Dashboard Admin" subtitle="Ringkasan aktivitas sistem akademik." />

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <StatCard label="Total Pengguna" :value="stats.total_users" icon="👥" accent="indigo" />
            <StatCard label="Dosen" :value="stats.total_lecturers" icon="🧑‍🏫" accent="sky" />
            <StatCard label="Mahasiswa" :value="stats.total_students" icon="🎓" accent="emerald" />
            <StatCard label="Mata Kuliah" :value="stats.total_courses" icon="📚" accent="amber" />
        </div>

        <Card class="mt-6">
            <template #header>
                <h2 class="font-semibold text-content">Aktivitas Terbaru</h2>
            </template>
            <DataTable :columns="columns" :rows="activities" empty-text="Belum ada aktivitas." />
        </Card>
    </AuthenticatedLayout>
</template>
