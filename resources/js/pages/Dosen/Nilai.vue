<script setup>
import { computed, ref, watch } from 'vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import Card from '@/components/ui/Card.vue';
import Button from '@/components/ui/Button.vue';
import Select from '@/components/ui/Select.vue';
import Badge from '@/components/ui/Badge.vue';
import ConfirmDialog from '@/components/shared/ConfirmDialog.vue';

const props = defineProps({
    offerings: { type: Array, required: true },
    students: { type: Array, required: true },
    selected_offering_id: { type: [Number, String], default: null },
});

const page = usePage();

const selectedOffering = ref(props.selected_offering_id ?? (props.offerings[0]?.id ?? ''));
const confirmOpen = ref(false);

const offeringOptions = computed(() =>
    props.offerings.map((o) => ({ value: o.id, label: o.label })),
);

function normalize(student) {
    return {
        study_plan_detail_id: student.study_plan_detail_id,
        student_id: student.student_id,
        nim: student.nim,
        name: student.name,
        assignment: student.assignment_score ?? '',
        midterm: student.midterm_score ?? '',
        final: student.final_score ?? '',
        score: student.score ?? '',
    };
}

const rows = ref(props.students.map(normalize));

watch(
    () => props.students,
    (students) => {
        rows.value = students.map(normalize);
    },
);

watch(selectedOffering, (value) => {
    if (value === '' || value === null) return;
    router.get(
        route('dosen.nilai.index'),
        { offering: value },
        { preserveState: true, preserveScroll: true, replace: true },
    );
});

const form = useForm({
    course_offering_id: null,
    grades: [],
});

function toNum(value) {
    if (value === null || value === undefined || value === '') return null;
    const num = Number(value);
    return Number.isNaN(num) ? null : num;
}

function finalScore(row) {
    const assignment = toNum(row.assignment);
    const midterm = toNum(row.midterm);
    const final = toNum(row.final);

    if (assignment !== null && midterm !== null && final !== null) {
        return Math.round((0.2 * assignment + 0.3 * midterm + 0.5 * final) * 100) / 100;
    }

    return toNum(row.score);
}

function letterAndPoint(score) {
    if (score === null) return { letter: null, point: null };

    let letter;
    let point;

    if (score >= 85) { letter = 'A'; point = 4.0; }
    else if (score >= 80) { letter = 'A-'; point = 3.75; }
    else if (score >= 75) { letter = 'B+'; point = 3.25; }
    else if (score >= 70) { letter = 'B'; point = 3.0; }
    else if (score >= 65) { letter = 'B-'; point = 2.75; }
    else if (score >= 60) { letter = 'C+'; point = 2.25; }
    else if (score >= 55) { letter = 'C'; point = 2.0; }
    else if (score >= 40) { letter = 'D'; point = 1.0; }
    else { letter = 'E'; point = 0; }

    return { letter, point };
}

function preview(row) {
    const score = finalScore(row);
    const { letter, point } = letterAndPoint(score);
    return { score, letter, point };
}

function buildGrades() {
    return rows.value.map((row) => ({
        study_plan_detail_id: row.study_plan_detail_id,
        student_id: row.student_id,
        assignment_score: toNum(row.assignment),
        midterm_score: toNum(row.midterm),
        final_score: toNum(row.final),
        score: toNum(row.score),
    }));
}

function save() {
    form.course_offering_id = Number(selectedOffering.value);
    form.grades = buildGrades();
    form.post(route('dosen.nilai.store'), {
        preserveScroll: true,
        onSuccess: () => {
            confirmOpen.value = false;
        },
    });
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Input Nilai" />

        <PageHeader title="Input Nilai" subtitle="Kelola nilai mahasiswa pada kelas yang Anda ampu." />

        <div v-if="page.props.flash?.success" class="mb-4 rounded-md bg-success-subtle p-3 text-sm text-success-strong">
            {{ page.props.flash.success }}
        </div>

        <div v-if="form.hasErrors" class="mb-4 rounded-md bg-danger-subtle p-3 text-sm text-danger-strong">
            <p v-if="form.errors.grades">{{ form.errors.grades }}</p>
            <p v-else>Periksa kembali input nilai. Pastikan setiap mahasiswa memiliki komponen nilai atau skor akhir (0–100).</p>
        </div>

        <Card class="mb-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <Select
                    v-model="selectedOffering"
                    label="Kelas"
                    :options="offeringOptions"
                    placeholder="Pilih kelas…"
                />
            </div>
        </Card>

        <Card>
            <template #header>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="font-semibold text-content">Daftar Mahasiswa</h2>
                        <p class="text-sm text-muted">
                            Isi komponen nilai (Tugas 20% + UTS 30% + UAS 50%) atau skor akhir langsung.
                        </p>
                    </div>
                    <Button variant="primary" :disabled="rows.length === 0" @click="confirmOpen = true">
                        💾 Simpan Nilai
                    </Button>
                </div>
            </template>

            <div v-if="rows.length === 0" class="py-8 text-center text-sm text-muted">
                Belum ada mahasiswa terdaftar pada kelas ini.
            </div>

            <div v-else class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wide text-muted">
                            <th class="px-3 py-2">NIM</th>
                            <th class="px-3 py-2">Nama</th>
                            <th class="px-3 py-2">Tugas</th>
                            <th class="px-3 py-2">UTS</th>
                            <th class="px-3 py-2">UAS</th>
                            <th class="px-3 py-2">Skor Akhir</th>
                            <th class="px-3 py-2">Huruf</th>
                            <th class="px-3 py-2">Grade Point</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr v-for="row in rows" :key="row.study_plan_detail_id">
                            <td class="px-3 py-2 font-mono text-muted">{{ row.nim }}</td>
                            <td class="px-3 py-2 font-medium text-content">{{ row.name }}</td>
                            <td class="px-3 py-2">
                                <input
                                    v-model="row.assignment"
                                    type="number"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                    placeholder="—"
                                    class="w-20 rounded-md border border-border-strong px-2 py-1.5 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-focus"
                                />
                            </td>
                            <td class="px-3 py-2">
                                <input
                                    v-model="row.midterm"
                                    type="number"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                    placeholder="—"
                                    class="w-20 rounded-md border border-border-strong px-2 py-1.5 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-focus"
                                />
                            </td>
                            <td class="px-3 py-2">
                                <input
                                    v-model="row.final"
                                    type="number"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                    placeholder="—"
                                    class="w-20 rounded-md border border-border-strong px-2 py-1.5 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-focus"
                                />
                            </td>
                            <td class="px-3 py-2">
                                <div class="flex flex-col gap-1">
                                    <input
                                        v-model="row.score"
                                        type="number"
                                        min="0"
                                        max="100"
                                        step="0.01"
                                        placeholder="Otomatis"
                                        class="w-24 rounded-md border border-border-strong px-2 py-1.5 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-focus"
                                    />
                                    <span v-if="preview(row).score !== null" class="text-xs text-muted">
                                        ≈ {{ preview(row).score }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-3 py-2">
                                <Badge v-if="preview(row).letter" variant="indigo">{{ preview(row).letter }}</Badge>
                                <span v-else class="text-muted">—</span>
                            </td>
                            <td class="px-3 py-2 font-medium text-content">
                                {{ preview(row).point !== null ? preview(row).point.toFixed(2) : '—' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>

        <ConfirmDialog
            :open="confirmOpen"
            title="Simpan Nilai"
            message="Simpan nilai seluruh mahasiswa pada kelas ini? Huruf dan grade point akan dihitung ulang oleh sistem."
            confirm-label="Simpan"
            :danger="false"
            :processing="form.processing"
            @close="confirmOpen = false"
            @confirm="save"
        />
    </AuthenticatedLayout>
</template>
