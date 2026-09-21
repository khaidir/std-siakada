<script setup>
import { ref } from 'vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import Card from '@/components/ui/Card.vue';
import Button from '@/components/ui/Button.vue';
import Input from '@/components/ui/Input.vue';
import Textarea from '@/components/ui/Textarea.vue';
import Badge from '@/components/ui/Badge.vue';
import EmptyState from '@/components/ui/EmptyState.vue';

const props = defineProps({
    assignment: { type: Object, required: true },
    submissions: { type: Array, required: true },
});

const page = usePage();

const form = useForm({
    grades: [],
});

const grades = ref(
    props.submissions.map((s) => ({
        submission_id: s.id,
        score: s.score ?? '',
        feedback: s.feedback ?? '',
    })),
);

function save() {
    form.grades = grades.value.map((g) => ({
        submission_id: g.submission_id,
        score: g.score === '' ? 0 : Number(g.score),
        feedback: g.feedback || null,
    }));

    form.post(route('dosen.tugas.nilai.store', { assignment: props.assignment.id }), {
        preserveScroll: true,
        onSuccess: () => {
            // Refresh data
            router.reload({ preserveScroll: true });
        },
    });
}

function formatDate(value) {
    if (!value) return '—';
    return value;
}
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="'Penilaian: ' + assignment.title" />

        <PageHeader
            :title="'Penilaian: ' + assignment.title"
            subtitle="Input skor dan feedback untuk setiap submission mahasiswa."
        >
            <template #actions>
                <Button variant="secondary" @click="router.visit(route('dosen.tugas.index'))">
                    ← Kembali
                </Button>
            </template>
        </PageHeader>

        <div v-if="page.props.flash?.success" class="mb-4 rounded-md bg-emerald-50 p-3 text-sm text-emerald-700">
            {{ page.props.flash.success }}
        </div>

        <div v-if="form.hasErrors" class="mb-4 rounded-md bg-rose-50 p-3 text-sm text-rose-700">
            <p v-for="(err, key) in form.errors" :key="key">{{ err }}</p>
        </div>

        <Card class="mb-6">
            <div class="flex items-center gap-4 text-sm">
                <span class="text-muted">Skor Maksimal:</span>
                <Badge variant="info">{{ assignment.max_score }}</Badge>
                <span class="text-muted">Total Submission:</span>
                <Badge variant="success">{{ submissions.length }}</Badge>
            </div>
        </Card>

        <Card>
            <template #header>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="font-semibold text-content">Daftar Submission</h2>
                        <p class="text-sm text-muted">Nilai submission mahasiswa untuk tugas ini.</p>
                    </div>
                    <Button variant="primary" :disabled="submissions.length === 0" @click="save">
                        💾 Simpan Semua Nilai
                    </Button>
                </div>
            </template>

            <div v-if="submissions.length === 0" class="py-8">
                <EmptyState title="Belum ada submission" description="Belum ada mahasiswa yang mengumpulkan tugas ini." />
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-border text-xs uppercase text-muted">
                            <th class="px-4 py-3 font-medium">NIM</th>
                            <th class="px-4 py-3 font-medium">Nama</th>
                            <th class="px-4 py-3 font-medium">File</th>
                            <th class="px-4 py-3 font-medium">Waktu Submit</th>
                            <th class="px-4 py-3 font-medium">Skor (0-{{ assignment.max_score }})</th>
                            <th class="px-4 py-3 font-medium">Feedback</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr v-for="(grade, index) in grades" :key="grade.submission_id">
                            <td class="px-4 py-3 font-mono text-xs">{{ submissions[index]?.nim }}</td>
                            <td class="px-4 py-3">{{ submissions[index]?.student_name }}</td>
                            <td class="px-4 py-3">
                                <a
                                    v-if="submissions[index]?.file_url"
                                    :href="submissions[index].file_url"
                                    target="_blank"
                                    class="text-indigo-600 hover:text-indigo-800"
                                >
                                    📎 Unduh
                                </a>
                                <span v-else class="text-muted">—</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-muted">
                                {{ formatDate(submissions[index]?.submitted_at) }}
                            </td>
                            <td class="px-4 py-3">
                                <input
                                    v-model="grade.score"
                                    type="number"
                                    :min="0"
                                    :max="assignment.max_score"
                                    step="0.01"
                                    class="w-24 rounded-md border border-border px-2 py-1 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    :class="{ 'border-rose-400': grade.score !== '' && (Number(grade.score) < 0 || Number(grade.score) > Number(assignment.max_score)) }"
                                />
                            </td>
                            <td class="px-4 py-3">
                                <input
                                    v-model="grade.feedback"
                                    type="text"
                                    placeholder="Feedback (opsional)"
                                    class="w-40 rounded-md border border-border px-2 py-1 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>
    </AuthenticatedLayout>
</template>
