<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import Card from '@/components/ui/Card.vue';
import Badge from '@/components/ui/Badge.vue';
import Table from '@/components/ui/Table.vue';
import EmptyState from '@/components/ui/EmptyState.vue';

const props = defineProps({
    student: Object,
    semesterGroups: Array,
    ipk: Number,
    totalSks: Number,
});

const columns = [
    { key: 'no', label: 'No' },
    { key: 'course_code', label: 'Kode MK' },
    { key: 'course_name', label: 'Mata Kuliah' },
    { key: 'sks', label: 'SKS' },
    { key: 'letter_grade', label: 'Nilai' },
    { key: 'grade_point', label: 'Bobot' },
];

function semesterLabel(semester) {
    if (!semester) return 'Semester';
    const type = semester.type === 'ganjil' ? 'Ganjil' : semester.type === 'genap' ? 'Genap' : 'Pendek';
    return `${semester.academic_year?.code || ''} - ${type}`;
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
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Transkrip" />

        <PageHeader title="Transkrip Akademik" subtitle="Seluruh nilai hasil belajar selama masa studi." />

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

        <!-- Ringkasan IPK -->
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <Card>
                <template #header>
                    <h3 class="text-sm font-medium text-muted">IP Kumulatif (IPK)</h3>
                </template>
                <p class="text-3xl font-bold text-emerald-600">{{ ipk.toFixed(2) }}</p>
            </Card>
            <Card>
                <template #header>
                    <h3 class="text-sm font-medium text-muted">Total SKS</h3>
                </template>
                <p class="text-3xl font-bold text-content">{{ totalSks }}</p>
            </Card>
        </div>

        <!-- Per Semester -->
        <div v-for="(group, idx) in semesterGroups" :key="idx" class="mb-6">
            <Card>
                <template #header>
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="font-semibold text-content">{{ semesterLabel(group.semester) }}</h2>
                            <p class="text-sm text-muted">IP: {{ group.ip.toFixed(2) }} &middot; SKS: {{ group.total_sks }}</p>
                        </div>
                    </div>
                </template>

                <Table :columns="columns" :rows="group.grades.map((g, i) => ({
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
        </div>

        <div v-if="semesterGroups.length === 0" class="py-8">
            <Card>
                <EmptyState title="Belum ada data" description="Belum ada data transkrip." />
            </Card>
        </div>
    </AuthenticatedLayout>
</template>
