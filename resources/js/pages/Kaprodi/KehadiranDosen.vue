<script setup>
import { ref } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import Card from '@/components/ui/Card.vue';
import Button from '@/components/ui/Button.vue';
import Select from '@/components/ui/Select.vue';
import Badge from '@/components/ui/Badge.vue';
import StatCard from '@/components/shared/StatCard.vue';
import EmptyState from '@/components/ui/EmptyState.vue';

const props = defineProps({
    summary: { type: Array, required: true },
    stats: { type: Object, required: true },
    semesters: { type: Array, required: true },
    filters: { type: Object, default: () => ({}) },
});

const page = usePage();

const selectedSemester = ref(props.filters.semester_id ?? '');

function applyFilter() {
    router.get(
        route('kaprodi.kehadiran.index'),
        { semester_id: selectedSemester.value || '' },
        { preserveState: true, preserveScroll: true, replace: true },
    );
}

function viewDetail(lecturerId) {
    router.visit(route('kaprodi.kehadiran.show', { lecturerId }));
}

function percentageColor(pct) {
    if (pct >= 85) return 'text-emerald-600';
    if (pct >= 70) return 'text-amber-600';
    return 'text-rose-600';
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Monitoring Kehadiran Dosen" />

        <PageHeader title="Monitoring Kehadiran Dosen" subtitle="Rekap kehadiran mengajar dosen prodi Anda." />

        <div v-if="page.props.flash?.success" class="mb-4 rounded-md bg-emerald-50 p-3 text-sm text-emerald-700">
            {{ page.props.flash.success }}
        </div>

        <!-- Statistik Ringkas -->
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <StatCard title="Total Dosen" :value="stats.total_lecturers" icon="👨‍🏫" />
            <StatCard title="Rata-rata Kehadiran" :value="stats.avg_percentage + '%'" icon="📊" />
        </div>

        <!-- Filter -->
        <Card class="mb-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <Select
                    v-model="selectedSemester"
                    label="Semester"
                    :options="semesters.map(s => ({ value: s.id, label: s.label }))"
                    placeholder="Semua Semester"
                />
                <div class="flex items-end">
                    <Button variant="primary" @click="applyFilter">🔍 Terapkan Filter</Button>
                </div>
            </div>
        </Card>

        <!-- Tabel Rekap -->
        <Card>
            <template #header>
                <h2 class="font-semibold text-content">Rekap Kehadiran Dosen</h2>
            </template>

            <div v-if="summary.length === 0" class="py-8">
                <EmptyState title="Tidak ada data" description="Belum ada data kehadiran dosen untuk filter yang dipilih." />
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-border text-xs uppercase text-muted">
                            <th class="px-4 py-3 font-medium">NIDN</th>
                            <th class="px-4 py-3 font-medium">Nama</th>
                            <th class="px-4 py-3 font-medium">Prodi</th>
                            <th class="px-4 py-3 font-medium">Pertemuan</th>
                            <th class="px-4 py-3 font-medium">Hadir</th>
                            <th class="px-4 py-3 font-medium">Terlambat</th>
                            <th class="px-4 py-3 font-medium">Izin</th>
                            <th class="px-4 py-3 font-medium">Alpha</th>
                            <th class="px-4 py-3 font-medium">% Kehadiran</th>
                            <th class="px-4 py-3 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr v-for="item in summary" :key="item.lecturer_id" class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-mono text-xs">{{ item.nidn }}</td>
                            <td class="px-4 py-3 font-medium">{{ item.lecturer_name }}</td>
                            <td class="px-4 py-3 text-xs text-muted">{{ item.study_program }}</td>
                            <td class="px-4 py-3">{{ item.total_pertemuan }}</td>
                            <td class="px-4 py-3">
                                <Badge variant="success">{{ item.total_hadir }}</Badge>
                            </td>
                            <td class="px-4 py-3">
                                <Badge variant="warning">{{ item.total_terlambat }}</Badge>
                            </td>
                            <td class="px-4 py-3">
                                <Badge variant="info">{{ item.total_izin }}</Badge>
                            </td>
                            <td class="px-4 py-3">
                                <Badge variant="danger">{{ item.total_alpha }}</Badge>
                            </td>
                            <td class="px-4 py-3">
                                <span :class="percentageColor(item.persentase)" class="font-semibold">
                                    {{ item.persentase }}%
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <Button variant="ghost" size="sm" @click="viewDetail(item.lecturer_id)">
                                    📋 Detail
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>
    </AuthenticatedLayout>
</template>
