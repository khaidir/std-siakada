<script setup>
import { ref } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import Card from '@/components/ui/Card.vue';
import Button from '@/components/ui/Button.vue';
import Badge from '@/components/ui/Badge.vue';
import Modal from '@/components/ui/Modal.vue';
import EmptyState from '@/components/ui/EmptyState.vue';

const props = defineProps({
    assignments: { type: Array, default: () => [] },
});

const page = usePage();

const submitModalOpen = ref(false);
const selectedAssignment = ref(null);
const selectedFile = ref(null);
const isSubmitting = ref(false);

function openSubmitModal(assignment) {
    selectedAssignment.value = assignment;
    selectedFile.value = null;
    submitModalOpen.value = true;
}

function closeSubmitModal() {
    submitModalOpen.value = false;
    selectedAssignment.value = null;
    selectedFile.value = null;
}

function onFileChange(event) {
    selectedFile.value = event.target.files[0] || null;
}

function submitAssignment() {
    if (!selectedFile.value || !selectedAssignment.value) return;

    isSubmitting.value = true;

    const form = new FormData();
    form.append('assignment_id', selectedAssignment.value.id);
    form.append('file', selectedFile.value);

    router.post(route('mahasiswa.tugas.submit'), form, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            closeSubmitModal();
            isSubmitting.value = false;
        },
        onError: () => {
            isSubmitting.value = false;
        },
        onFinish: () => {
            isSubmitting.value = false;
        },
    });
}

function formatDate(value) {
    if (!value) return '—';
    const date = new Date(value);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function statusBadgeVariant(assignment) {
    if (assignment.submission) return 'emerald';
    if (assignment.is_past_due) return 'rose';
    return 'amber';
}

function statusLabel(assignment) {
    if (assignment.submission) return 'Sudah Dikumpul';
    if (assignment.is_past_due) return 'Terlewat';
    return 'Belum Dikumpul';
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Tugas" />

        <PageHeader title="Tugas" subtitle="Kumpulkan tugas perkuliahan dari kelas yang Anda ikuti." />

        <div v-if="page.props.flash?.success" class="mb-4 rounded-md bg-success-subtle p-3 text-sm text-success-strong">
            {{ page.props.flash.success }}
        </div>

        <div v-if="page.props.errors?.length" class="mb-4 rounded-md bg-danger-subtle p-3 text-sm text-danger-strong">
            <p v-for="(err, i) in page.props.errors" :key="i">{{ err }}</p>
        </div>

        <Card>
            <template #header>
                <div>
                    <h2 class="font-semibold text-content">Daftar Tugas</h2>
                    <p class="text-sm text-muted">Tugas dari kelas yang sedang Anda ikuti.</p>
                </div>
            </template>

            <div v-if="assignments.length === 0" class="py-8">
                <EmptyState title="Belum ada tugas" description="Belum ada tugas yang diberikan oleh dosen." />
            </div>

            <div v-else class="divide-y divide-border">
                <div
                    v-for="assignment in assignments"
                    :key="assignment.id"
                    class="flex flex-col gap-3 px-3 py-4 sm:flex-row sm:items-start sm:justify-between"
                >
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="font-medium text-content">{{ assignment.title }}</h3>
                            <Badge :variant="statusBadgeVariant(assignment)">
                                {{ statusLabel(assignment) }}
                            </Badge>
                        </div>
                        <p class="mt-1 text-xs text-muted">
                            {{ assignment.course_code }} — {{ assignment.course_name }}
                        </p>
                        <p v-if="assignment.description" class="mt-2 text-sm text-muted line-clamp-2">
                            {{ assignment.description }}
                        </p>
                        <div class="mt-2 flex flex-wrap items-center gap-4 text-xs text-muted">
                            <span>📅 Tenggat: {{ formatDate(assignment.due_date) }}</span>
                            <span>🎯 Skor Maks: {{ assignment.max_score }}</span>
                        </div>

                        <!-- Info submission -->
                        <div v-if="assignment.submission" class="mt-3 rounded-md bg-success-subtle p-3 text-sm">
                            <p class="font-medium text-success-strong">✅ Telah dikumpulkan</p>
                            <p class="mt-1 text-success-strong">
                                {{ formatDate(assignment.submission.submitted_at) }}
                            </p>
                            <div v-if="assignment.submission.score !== null" class="mt-2">
                                <span class="font-medium text-success-strong">Nilai: {{ assignment.submission.score }}</span>
                            </div>
                            <p v-if="assignment.submission.feedback" class="mt-1 text-success-strong">
                                Feedback: {{ assignment.submission.feedback }}
                            </p>
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-2">
                        <Button
                            v-if="!assignment.submission && !assignment.is_past_due"
                            variant="primary"
                            size="sm"
                            @click="openSubmitModal(assignment)"
                        >
                            📤 Kumpul
                        </Button>
                        <Button
                            v-else-if="assignment.submission"
                            variant="outline"
                            size="sm"
                            @click="openSubmitModal(assignment)"
                        >
                            🔄 Kumpul Ulang
                        </Button>
                        <Button
                            v-else
                            variant="outline"
                            size="sm"
                            disabled
                        >
                            🔒 Lewat
                        </Button>
                    </div>
                </div>
            </div>
        </Card>

        <!-- Modal Submit -->
        <Modal :open="submitModalOpen" @close="closeSubmitModal">
            <template #title>Kumpulkan Tugas</template>
            <template #description>
                <p v-if="selectedAssignment" class="text-sm text-muted">
                    {{ selectedAssignment.title }} — {{ selectedAssignment.course_code }}
                </p>
            </template>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-content">Upload File</label>
                    <input
                        type="file"
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.jpg,.jpeg,.png"
                        class="mt-1 block w-full rounded-md border border-border bg-surface px-3 py-2 text-sm text-content file:mr-3 file:rounded file:border-0 file:bg-primary-subtle file:px-3 file:py-1 file:text-sm file:font-medium file:text-primary-strong hover:file:bg-primary-subtle"
                        @change="onFileChange"
                    />
                    <p class="mt-1 text-xs text-muted">
                        Format: PDF, DOC, XLS, PPT, ZIP, RAR, JPG, PNG (maks. 10 MB)
                    </p>
                </div>

                <div v-if="selectedFile" class="rounded-md bg-neutral-subtle p-3 text-sm text-content">
                    📎 {{ selectedFile.name }} ({{ (selectedFile.size / 1024).toFixed(1) }} KB)
                </div>
            </div>

            <template #footer>
                <div class="flex justify-end gap-3">
                    <Button variant="outline" @click="closeSubmitModal">Batal</Button>
                    <Button
                        variant="primary"
                        :disabled="!selectedFile || isSubmitting"
                        @click="submitAssignment"
                    >
                        {{ isSubmitting ? 'Mengirim…' : '📤 Kumpul' }}
                    </Button>
                </div>
            </template>
        </Modal>
    </AuthenticatedLayout>
</template>
