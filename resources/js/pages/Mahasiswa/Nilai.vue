<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import Card from '@/components/ui/Card.vue';
import Badge from '@/components/ui/Badge.vue';
import Table from '@/components/ui/Table.vue';
import EmptyState from '@/components/ui/EmptyState.vue';

const props = defineProps({
    grades: { type: Array, default: () => [] },
    gpa: { type: Number, default: 0 },
    total_sks: { type: Number, default: 0 },
});

const columns = [
    { key: 'course_code', label: 'Kode MK' },
    { key: 'course_name', label: 'Nama MK' },
    { key: 'sks', label: 'SKS' },
    { key: 'score', label: 'Skor' },
    { key: 'letter_grade', label: 'Huruf' },
    { key: 'grade_point', label: 'Bobot' },
];

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

const gpaColor = computed(() => {
    if (props.gpa >= 3.5) return 'text-success-strong';
    if (props.gpa >= 2.75) return 'text-info-strong';
    if (props.gpa >= 2.0) return 'text-warning-strong';
    return 'text-danger-strong';
});
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Nilai" />

        <PageHeader title="Nilai Hasil Belajar" subtitle="Ringkasan nilai dan performa akademik Anda." />

        <!-- Ringkasan -->
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <Card>
                <template #header>
                    <h3 class="text-sm font-medium text-muted">IPK</h3>
                </template>
                <p class="text-3xl font-bold" :class="gpaColor">{{ gpa.toFixed(2) }}</p>
            </Card>
            <Card>
                <template #header>
                    <h3 class="text-sm font-medium text-muted">Total SKS</h3>
                </template>
                <p class="text-3xl font-bold text-content">{{ total_sks }}</p>
            </Card>
            <Card>
                <template #header>
                    <h3 class="text-sm font-medium text-muted">Jumlah Mata Kuliah</h3>
                </template>
                <p class="text-3xl font-bold text-content">{{ grades.length }}</p>
            </Card>
        </div>

        <!-- Tabel Nilai -->
        <Card>
            <template #header>
                <div>
                    <h2 class="font-semibold text-content">Daftar Nilai</h2>
                    <p class="text-sm text-muted">Nilai mata kuliah yang telah ditempuh.</p>
                </div>
            </template>

            <div v-if="grades.length === 0" class="py-8">
                <EmptyState title="Belum ada nilai" description="Belum ada nilai yang tercatat." />
            </div>

            <Table v-else :columns="columns" :rows="grades">
                <template #cell-letter_grade="{ row }">
                    <Badge :variant="gradeBadgeVariant(row.letter_grade)">
                        {{ row.letter_grade }}
                    </Badge>
                </template>
                <template #cell-grade_point="{ row }">
                    <span class="font-medium">{{ row.grade_point.toFixed(2) }}</span>
                </template>
            </Table>
        </Card>
    </AuthenticatedLayout>
</template>
