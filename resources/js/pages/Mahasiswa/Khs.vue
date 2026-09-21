<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import Card from '@/components/ui/Card.vue';
import Badge from '@/components/ui/Badge.vue';
import Table from '@/components/ui/Table.vue';
import EmptyState from '@/components/ui/EmptyState.vue';

const props = defineProps({
    student: Object,
    semesters: Array,
    selectedSemesterId: Number,
    grades: Array,
    ipSemester: Number,
    ipk: Number,
    totalSksSemester: Number,
    totalSksKumulatif: Number,
});

const localSemesterId = ref(props.selectedSemesterId);

const columns = [
    { key: 'no', label: 'No' },
    { key: 'course_code', label: 'Kode MK' },
    { key: 'course_name', label: 'Mata Kuliah' },
    { key: 'sks', label: 'SKS' },
    { key: 'letter_grade', label: 'Nilai' },
    { key: 'grade_point', label: 'Bobot' },
];

function semesterLabel(semester) {
    const type = semester.type === 'ganjil' ? 'Ganjil' : semester.type === 'genap' ? 'Genap' : 'Pendek';
    return `${semester.academic_year.code} - ${type}`;
}

function gradeBadgeVariant(letter) {
    const map = {
        'A': 'emerald',
        'A-': 'emerald',
        'B+': 'blue',
        'B': 'blue',
        'B-': 'blue',
        'C+': 'amber',
        'C': 'amber',
        'D': 'rose',
        'E': 'rose',
    };
    return map[letter] ?? 'slate';
}

const ipColor = computed(() => {
    if (props.ipSemester >= 3.5) return 'text-emerald-600';
    if (props.ipSemester >= 2.75) return 'text-blue-600';
    if (props.ipSemester >= 2.0) return 'text-amber-600';
    return 'text-rose-600';
});

const ipkColor = computed(() => {
    if (props.ipk >= 3.5) return 'text-emerald-600';
    if (props.ipk >= 2.75) return 'text-blue-600';
    if (props.ipk >= 2.0) return 'text-amber-600';
    return 'text-rose-600';
});

function changeSemester() {
    router.get(route('mahasiswa.khs.index'), { semester_id: localSemesterId.value }, { preserveState: true });
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="KHS" />

        <PageHeader title="Kartu Hasil Studi (KHS)" subtitle="Nilai hasil belajar per semester." />

        <!-- Info Mahasiswa -->
        <Card class="mb-6">
            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                <div>
                    <span class="text-sm text-muted">Nama</span>
                    <p class="font-medium text-content">{{ student.user?.name }}</p>
                </div>
                <div>
                    <span class="text-sm text-muted">NIM</span>
                    <p class="font-medium text-content">{{ student.nim }}</p>
                </div>
                <div>
                    <span class="text-sm text-muted">Program Studi</span>
                    <p class="font-medium text-content">{{ student.study_program?.name }}</p>
                </div>
                <div>
                    <span class="text-sm text-muted">Tahun Masuk</span>
                    <p class="font-medium text-content">{{ student.entry_year }}</p>
                </div>
            </div>
        </Card>

        <!-- Pilih Semester -->
        <Card class="mb-6">
            <template #header>
                <h3 class="text-sm font-medium text-content">Pilih Semester</h3>
            </template>
            <select
                v-model="localSemesterId"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:w-64"
                @change="changeSemester"
            >
                <option v-for="s in semesters" :key="s.id" :value="s.id">
                    {{ semesterLabel(s) }}
                </option>
            </select>
        </Card>

        <!-- Ringkasan IP -->
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <Card>
                <template #header>
                    <h3 class="text-sm font-medium text-muted">IP Semester</h3>
                </template>
                <p class="text-3xl font-bold" :class="ipColor">{{ ipSemester.toFixed(2) }}</p>
            </Card>
            <Card>
                <template #header>
                    <h3 class="text-sm font-medium text-muted">IP Kumulatif (IPK)</h3>
                </template>
                <p class="text-3xl font-bold" :class="ipkColor">{{ ipk.toFixed(2) }}</p>
            </Card>
            <Card>
                <template #header>
                    <h3 class="text-sm font-medium text-muted">SKS Semester</h3>
                </template>
                <p class="text-3xl font-bold text-content">{{ totalSksSemester }}</p>
            </Card>
            <Card>
                <template #header>
                    <h3 class="text-sm font-medium text-muted">Total SKS</h3>
                </template>
                <p class="text-3xl font-bold text-content">{{ totalSksKumulatif }}</p>
            </Card>
        </div>

        <!-- Tabel Nilai -->
        <Card>
            <template #header>
                <div>
                    <h2 class="font-semibold text-content">Daftar Nilai</h2>
                    <p class="text-sm text-muted">Nilai mata kuliah pada semester terpilih.</p>
                </div>
            </template>

            <div v-if="grades.length === 0" class="py-8">
                <EmptyState title="Belum ada nilai" description="Belum ada nilai untuk semester ini." />
            </div>

            <Table v-else :columns="columns" :rows="grades.map((g, i) => ({
                no: i + 1,
                course_code: g.study_plan_detail?.course_offering?.course?.code,
                course_name: g.study_plan_detail?.course_offering?.course?.name,
                sks: g.study_plan_detail?.course_offering?.course?.sks,
                letter_grade: g.letter_grade,
                grade_point: g.grade_point,
            }))">
                <template #cell-letter_grade="{ row }">
                    <Badge :variant="gradeBadgeVariant(row.letter_grade)">
                        {{ row.letter_grade }}
                    </Badge>
                </template>
                <template #cell-grade_point="{ row }">
                    <span class="font-medium">{{ row.grade_point?.toFixed(2) ?? '-' }}</span>
                </template>
            </Table>
        </Card>
    </AuthenticatedLayout>
</template>
