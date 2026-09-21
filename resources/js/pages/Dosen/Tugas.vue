<script setup>
import { computed, ref, watch } from 'vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import Card from '@/components/ui/Card.vue';
import Button from '@/components/ui/Button.vue';
import Select from '@/components/ui/Select.vue';
import Input from '@/components/ui/Input.vue';
import Textarea from '@/components/ui/Textarea.vue';
import Badge from '@/components/ui/Badge.vue';
import Modal from '@/components/ui/Modal.vue';
import ConfirmDialog from '@/components/shared/ConfirmDialog.vue';
import EmptyState from '@/components/ui/EmptyState.vue';

const props = defineProps({
    offerings: { type: Array, required: true },
    assignments: { type: Array, required: true },
    selected_offering_id: { type: [Number, String], default: null },
});

const page = usePage();

const selectedOffering = ref(props.selected_offering_id ?? (props.offerings[0]?.id ?? ''));
const showModal = ref(false);
const editMode = ref(false);
const editingId = ref(null);
const deleteConfirmOpen = ref(false);
const deletingId = ref(null);

const offeringOptions = computed(() =>
    props.offerings.map((o) => ({ value: o.id, label: o.label })),
);

watch(selectedOffering, (value) => {
    if (value === '' || value === null) return;
    router.get(
        route('dosen.tugas.index'),
        { offering: value },
        { preserveState: true, preserveScroll: true, replace: true },
    );
});

const form = useForm({
    course_offering_id: null,
    title: '',
    description: '',
    due_date: '',
    max_score: 100,
});

function openCreate() {
    editMode.value = false;
    editingId.value = null;
    form.course_offering_id = Number(selectedOffering.value);
    form.title = '';
    form.description = '';
    form.due_date = '';
    form.max_score = 100;
    showModal.value = true;
}

function openEdit(assignment) {
    editMode.value = true;
    editingId.value = assignment.id;
    form.course_offering_id = Number(selectedOffering.value);
    form.title = assignment.title;
    form.description = assignment.description ?? '';
    form.due_date = assignment.due_date ?? '';
    form.max_score = assignment.max_score;
    showModal.value = true;
}

function confirmDelete(id) {
    deletingId.value = id;
    deleteConfirmOpen.value = true;
}

function save() {
    if (editMode.value) {
        form.post(route('dosen.tugas.update', { id: editingId.value }), {
            _method: 'PUT',
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
            },
        });
    } else {
        form.post(route('dosen.tugas.store'), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
            },
        });
    }
}

function destroy() {
    router.delete(route('dosen.tugas.destroy', { id: deletingId.value }), {
        preserveScroll: true,
        onSuccess: () => {
            deleteConfirmOpen.value = false;
            deletingId.value = null;
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
        <Head title="Kelola Tugas" />

        <PageHeader title="Kelola Tugas" subtitle="Buat dan kelola tugas untuk setiap kelas." />

        <div v-if="page.props.flash?.success" class="mb-4 rounded-md bg-emerald-50 p-3 text-sm text-emerald-700">
            {{ page.props.flash.success }}
        </div>

        <div v-if="form.hasErrors" class="mb-4 rounded-md bg-rose-50 p-3 text-sm text-rose-700">
            <p v-for="(err, key) in form.errors" :key="key">{{ err }}</p>
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
                        <h2 class="font-semibold text-content">Daftar Tugas</h2>
                        <p class="text-sm text-muted">Tugas yang telah dibuat untuk kelas ini.</p>
                    </div>
                    <Button variant="primary" :disabled="!selectedOffering" @click="openCreate">
                        ➕ Tambah Tugas
                    </Button>
                </div>
            </template>

            <div v-if="assignments.length === 0" class="py-8">
                <EmptyState title="Belum ada tugas" description="Buat tugas pertama untuk kelas ini." />
            </div>

            <div v-else class="divide-y divide-border">
                <div v-for="assignment in assignments" :key="assignment.id" class="flex items-start justify-between gap-4 px-3 py-4">
                    <div class="min-w-0 flex-1">
                        <h3 class="font-medium text-content">{{ assignment.title }}</h3>
                        <p v-if="assignment.description" class="mt-1 text-sm text-muted line-clamp-2">{{ assignment.description }}</p>
                        <div class="mt-2 flex flex-wrap items-center gap-3 text-xs text-muted">
                            <span>📅 Tenggat: {{ formatDate(assignment.due_date) }}</span>
                            <Badge variant="info">Skor Maks: {{ assignment.max_score }}</Badge>
                            <Badge variant="success">{{ assignment.submissions_count }} submission</Badge>
                        </div>
                    </div>
                    <div class="flex shrink-0 items-center gap-2">
                        <Button variant="ghost" size="sm" @click="openEdit(assignment)">
                            ✏️
                        </Button>
                        <Button variant="ghost" size="sm" @click="confirmDelete(assignment.id)">
                            🗑️
                        </Button>
                        <Button
                            variant="primary"
                            size="sm"
                            @click="router.visit(route('dosen.tugas.nilai.index', { assignment: assignment.id }))"
                        >
                            📝 Nilai
                        </Button>
                    </div>
                </div>
            </div>
        </Card>

        <!-- Modal Form -->
        <Modal :open="showModal" :title="editMode ? 'Edit Tugas' : 'Tambah Tugas'" @close="showModal = false">
            <form @submit.prevent="save" class="space-y-4">
                <Input
                    v-model="form.title"
                    label="Judul Tugas"
                    placeholder="Masukkan judul tugas"
                    required
                    :error="form.errors.title"
                />

                <Textarea
                    v-model="form.description"
                    label="Deskripsi"
                    placeholder="Deskripsi tugas (opsional)"
                    :error="form.errors.description"
                />

                <Input
                    v-model="form.due_date"
                    label="Tenggat Waktu"
                    type="datetime-local"
                    :error="form.errors.due_date"
                />

                <Input
                    v-model="form.max_score"
                    label="Skor Maksimal"
                    type="number"
                    min="1"
                    max="1000"
                    required
                    :error="form.errors.max_score"
                />

                <div class="flex justify-end gap-2 pt-2">
                    <Button variant="secondary" type="button" @click="showModal = false">
                        Batal
                    </Button>
                    <Button variant="primary" type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Menyimpan…' : 'Simpan' }}
                    </Button>
                </div>
            </form>
        </Modal>

        <!-- Confirm Delete -->
        <ConfirmDialog
            :show="deleteConfirmOpen"
            title="Hapus Tugas"
            message="Apakah Anda yakin ingin menghapus tugas ini? Semua submission terkait juga akan terhapus."
            confirm-label="Hapus"
            :danger="true"
            :processing="form.processing"
            @close="deleteConfirmOpen = false"
            @confirm="destroy"
        />
    </AuthenticatedLayout>
</template>
