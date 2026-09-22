<script setup>
import { ref, computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import Card from '@/components/ui/Card.vue';
import Button from '@/components/ui/Button.vue';
import Badge from '@/components/ui/Badge.vue';
import Table from '@/components/ui/Table.vue';
import Select from '@/components/ui/Select.vue';
import DataTable from '@/components/shared/DataTable.vue';
import EmptyState from '@/components/ui/EmptyState.vue';

const props = defineProps({
    active_tab: { type: String, default: 'khs' },
    filters: { type: Object, default: () => ({ semester_id: null, study_program_id: null }) },
    grade_reports: { type: Array, default: () => [] },
    attendance_reports: { type: Array, default: () => [] },
    lecturer_performance: { type: Object, default: () => ({ workload: [], attendance: [] }) },
});

const page = usePage();

const tabs = [
    { key: 'khs', label: 'KHS' },
    { key: 'presensi', label: 'Presensi Mahasiswa' },
    { key: 'kinerja_dosen', label: 'Kinerja Dosen' },
];

const activeTab = ref(props.active_tab);

const semesterOptions = computed(() => {
    const semesters = page.props.semesters ?? [];
    return semesters.map(s => ({ value: String(s.id), label: s.label }));
});

const studyProgramOptions = computed(() => {
    const programs = page.props.study_programs ?? [];
    return programs.map(p => ({ value: String(p.id), label: p.name }));
});

const selectedSemester = ref(props.filters.semester_id ? String(props.filters.semester_id) : '');
const selectedStudyProgram = ref(props.filters.study_program_id ? String(props.filters.study_program_id) : '');

function applyFilters() {
    router.get(route('pimpinan.laporan'), {
        tab: activeTab.value,
        semester_id: selectedSemester.value || '',
        study_program_id: selectedStudyProgram.value || '',
    }, {
        preserveState: true,
        preserveScroll: true,
    });
}

function switchTab(tab) {
    activeTab.value = tab;
    router.get(route('pimpinan.laporan'), {
        tab,
        semester_id: selectedSemester.value || '',
        study_program_id: selectedStudyProgram.value || '',
    }, {
        preserveState: true,
        preserveScroll: true,
    });
}

// KHS columns
const khsColumns = [
    { key: 'nim', label: 'NIM' },
    { key: 'student_name', label: 'Nama' },
    { key: 'study_program', label: 'Prodi' },
    { key: 'total_sks', label: 'Total SKS' },
    { key: 'gpa', label: 'IPK' },
];

// Attendance columns
const attendanceColumns = [
    { key: 'nim', label: 'NIM' },
    { key: 'student_name', label: 'Nama' },
    { key: 'total_sessions', label: 'Total Pertemuan' },
    { key: 'attended', label: 'Hadir' },
    { key: 'percentage', label: '% Kehadiran' },
];

// Lecturer workload columns
const workloadColumns = [
    { key: 'lecturer_name', label: 'Dosen' },
    { key: 'nidn', label: 'NIDN' },
    { key: 'total_classes', label: 'Jumlah Kelas' },
    { key: 'total_sks', label: 'Total SKS' },
];

// Lecturer attendance columns
const lecturerAttendanceColumns = [
    { key: 'lecturer_name', label: 'Dosen' },
    { key: 'nidn', label: 'NIDN' },
    { key: 'total_sessions', label: 'Total Sesi' },
    { key: 'attended_sessions', label: 'Hadir' },
    { key: 'attendance_percentage', label: '% Kehadiran' },
];

function formatPercentage(value) {
    return value + '%';
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Laporan Akademik" />

        <PageHeader title="Laporan Akademik" subtitle="Laporan KHS, presensi, dan kinerja dosen." breadcrumbs="Laporan" />

        <!-- Filters -->
        <Card class="mb-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <Select
                    v-model="selectedSemester"
                    label="Semester"
                    :options="semesterOptions"
                    placeholder="Semua Semester"
                />
                <Select
                    v-model="selectedStudyProgram"
                    label="Program Studi"
                    :options="studyProgramOptions"
                    placeholder="Semua Prodi"
                />
                <div class="flex items-end">
                    <Button variant="primary" @click="applyFilters">Terapkan Filter</Button>
                </div>
            </div>
        </Card>

        <!-- Tabs -->
        <div class="mb-6 border-b border-border">
            <nav class="flex gap-6">
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    class="border-b-2 px-1 py-3 text-sm font-medium transition-colors"
                    :class="activeTab === tab.key
                        ? 'border-primary text-primary-strong'
                        : 'border-transparent text-muted hover:text-content'"
                    @click="switchTab(tab.key)"
                >
                    {{ tab.label }}
                </button>
            </nav>
        </div>

        <!-- KHS Tab -->
        <div v-if="activeTab === 'khs'">
            <Card>
                <template #header>
                    <h3 class="font-semibold text-content">Kartu Hasil Studi (KHS)</h3>
                </template>
                <DataTable
                    :columns="khsColumns"
                    :rows="grade_reports"
                    empty-text="Belum ada data KHS untuk filter yang dipilih."
                >
                    <template #cell-gpa="{ value }">
                        {{ value }}
                    </template>
                </DataTable>
            </Card>
        </div>

        <!-- Presensi Tab -->
        <div v-if="activeTab === 'presensi'">
            <Card>
                <template #header>
                    <h3 class="font-semibold text-content">Presensi Mahasiswa</h3>
                </template>
                <DataTable
                    :columns="attendanceColumns"
                    :rows="attendance_reports"
                    empty-text="Belum ada data presensi untuk filter yang dipilih."
                >
                    <template #cell-percentage="{ value }">
                        <Badge :variant="value >= 75 ? 'emerald' : value >= 50 ? 'amber' : 'rose'">
                            {{ value }}%
                        </Badge>
                    </template>
                </DataTable>
            </Card>
        </div>

        <!-- Kinerja Dosen Tab -->
        <div v-if="activeTab === 'kinerja_dosen'" class="space-y-6">
            <Card>
                <template #header>
                    <h3 class="font-semibold text-content">Beban Mengajar Dosen</h3>
                </template>
                <DataTable
                    :columns="workloadColumns"
                    :rows="lecturer_performance.workload"
                    empty-text="Belum ada data beban mengajar untuk filter yang dipilih."
                />
            </Card>

            <Card>
                <template #header>
                    <h3 class="font-semibold text-content">Kehadiran Mengajar Dosen</h3>
                </template>
                <DataTable
                    :columns="lecturerAttendanceColumns"
                    :rows="lecturer_performance.attendance"
                    empty-text="Belum ada data kehadiran dosen untuk filter yang dipilih."
                >
                    <template #cell-attendance_percentage="{ value }">
                        <Badge :variant="value >= 90 ? 'emerald' : value >= 75 ? 'amber' : 'rose'">
                            {{ value }}%
                        </Badge>
                    </template>
                </DataTable>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>
