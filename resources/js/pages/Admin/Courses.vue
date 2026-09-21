<script setup>
import { ref } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import Card from '@/components/ui/Card.vue';
import Button from '@/components/ui/Button.vue';
import Badge from '@/components/ui/Badge.vue';
import Table from '@/components/ui/Table.vue';
import Modal from '@/components/ui/Modal.vue';
import Input from '@/components/ui/Input.vue';
import Select from '@/components/ui/Select.vue';
import ConfirmDialog from '@/components/shared/ConfirmDialog.vue';
import EmptyState from '@/components/ui/EmptyState.vue';

const props = defineProps({
    courses: { type: Array, default: () => [] },
});

const page = usePage();

const modalOpen = ref(false);
const isEditing = ref(false);
const editingId = ref(null);
const form = ref({
    study_program_id: null,
    code: '',
    name: '',
    sks: 3,
    semester: 1,
    type: 'wajib',
});

const deleteConfirmOpen = ref(false);
const deletingId = ref(null);

const typeOptions = [
    { value: 'wajib', label: 'Wajib' },
    { value: 'pilihan', label: 'Pilihan' },
];

const semesterOptions = Array.from({ length: 14 }, (_, i) => ({
    value: i + 1,
    label: `Semester ${i + 1}`,
}));

const columns = [
    { key: 'code', label: 'Kode' },
    { key: 'name', label: 'Nama' },
    { key: 'study_program_name', label: 'Prodi' },
    { key: 'sks', label: 'SKS' },
    { key: 'semester', label: 'Semester' },
    { key: 'type', label: 'Tipe' },
    { key: 'action', label: 'Aksi' },
];

function openCreateModal() {
    isEditing.value = false;
    editingId.value = null;
    form.value = { study_program_id: null, code: '', name: '', sks: 3, semester: 1, type: 'wajib' };
    modalOpen.value = true;
}

function openEditModal(course) {
    isEditing.value = true;
    editingId.value = course.id;
    form.value = {
        study_program_id: course.study_program_id,
        code: course.code,
        name: course.name,
        sks: course.sks,
        semester: course.semester,
        type: course.type,
    };
    modalOpen.value = true;
}

function closeModal() {
    modalOpen.value = false;
    editingId.value = null;
}

function save() {
    if (isEditing.value) {
        router.put(route('admin.courses.update', { id: editingId.value }), form.value, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => closeModal(),
        });
    } else {
        router.post(route('admin.courses.store'), form.value, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => closeModal(),
        });
    }
}

function confirmDelete(id) {
    deletingId.value = id;
    deleteConfirmOpen.value = true;
}

function deleteItem() {
    router.delete(route('admin.courses.destroy', { id: deletingId.value }), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            deleteConfirmOpen.value = false;
            deletingId.value = null;
        },
    });
}

function typeBadgeVariant(type) {
    return type === 'wajib' ? 'blue' : 'amber';
}

function typeLabel(type) {
    return type === 'wajib' ? 'Wajib' : 'Pilihan';
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Mata Kuliah" />

        <PageHeader title="Mata Kuliah" subtitle="Kelola semua mata kuliah di seluruh program studi." />

        <div v-if="page.props.flash?.success" class="mb-4 rounded-md bg-emerald-50 p-3 text-sm text-emerald-700">
            {{ page.props.flash.success }}
        </div>

        <Card>
            <template #header>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="font-semibold text-content">Daftar Mata Kuliah</h2>
                        <p class="text-sm text-muted">Semua mata kuliah yang terdaftar.</p>
                    </div>
                    <Button variant="primary" size="sm" @click="openCreateModal">
                        ➕ Tambah Mata Kuliah
                    </Button>
                </div>
            </template>

            <div v-if="courses.length === 0" class="py-8">
                <EmptyState title="Belum ada mata kuliah" description="Belum ada mata kuliah yang ditambahkan." />
            </div>

            <Table v-else :columns="columns" :rows="courses">
                <template #cell-type="{ row }">
                    <Badge :variant="typeBadgeVariant(row.type)">{{ typeLabel(row.type) }}</Badge>
                </template>
                <template #cell-action="{ row }">
                    <div class="flex gap-2">
                        <Button variant="outline" size="sm" @click="openEditModal(row)">✏️ Edit</Button>
                        <Button variant="danger" size="sm" @click="confirmDelete(row.id)">🗑️ Hapus</Button>
                    </div>
                </template>
            </Table>
        </Card>

        <Modal :open="modalOpen" @close="closeModal">
            <template #title>{{ isEditing ? 'Edit Mata Kuliah' : 'Tambah Mata Kuliah' }}</template>

            <div class="space-y-4">
                <Select v-model="form.study_program_id" label="Program Studi" :options="page.props.study_programs?.map(sp => ({ value: sp.id, label: sp.name })) ?? []" required />
                <Input v-model="form.code" label="Kode Mata Kuliah" placeholder="Contoh: IF101" required />
                <Input v-model="form.name" label="Nama Mata Kuliah" placeholder="Contoh: Algoritma Pemrograman" required />
                <div class="grid grid-cols-2 gap-4">
                    <Input v-model.number="form.sks" label="SKS" type="number" min="1" max="24" required />
                    <Select v-model="form.semester" label="Semester" :options="semesterOptions" required />
                </div>
                <Select v-model="form.type" label="Tipe" :options="typeOptions" required />
            </div>

            <template #footer>
                <div class="flex justify-end gap-3">
                    <Button variant="outline" @click="closeModal">Batal</Button>
                    <Button variant="primary" @click="save">{{ isEditing ? 'Simpan Perubahan' : 'Tambah' }}</Button>
                </div>
            </template>
        </Modal>

        <ConfirmDialog
            :open="deleteConfirmOpen"
            title="Hapus Mata Kuliah"
            message="Apakah Anda yakin ingin menghapus mata kuliah ini? Tindakan ini tidak dapat dibatalkan."
            confirm-label="Ya, Hapus"
            cancel-label="Batal"
            @confirm="deleteItem"
            @cancel="deleteConfirmOpen = false"
        />
    </AuthenticatedLayout>
</template>
