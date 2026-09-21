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
    study_programs: { type: Array, default: () => [] },
});

const page = usePage();

const modalOpen = ref(false);
const isEditing = ref(false);
const editingId = ref(null);
const form = ref({
    faculty_id: null,
    code: '',
    name: '',
    degree_level: 's1',
});

const deleteConfirmOpen = ref(false);
const deletingId = ref(null);

const degreeOptions = [
    { value: 'd3', label: 'D3' },
    { value: 'd4', label: 'D4' },
    { value: 's1', label: 'S1' },
    { value: 's2', label: 'S2' },
    { value: 's3', label: 'S3' },
    { value: 'profesi', label: 'Profesi' },
];

const columns = [
    { key: 'code', label: 'Kode' },
    { key: 'name', label: 'Nama' },
    { key: 'faculty_name', label: 'Fakultas' },
    { key: 'degree_level', label: 'Jenjang' },
    { key: 'action', label: 'Aksi' },
];

function openCreateModal() {
    isEditing.value = false;
    editingId.value = null;
    form.value = { faculty_id: null, code: '', name: '', degree_level: 's1' };
    modalOpen.value = true;
}

function openEditModal(sp) {
    isEditing.value = true;
    editingId.value = sp.id;
    form.value = {
        faculty_id: sp.faculty_id,
        code: sp.code,
        name: sp.name,
        degree_level: sp.degree_level,
    };
    modalOpen.value = true;
}

function closeModal() {
    modalOpen.value = false;
    editingId.value = null;
}

function save() {
    if (isEditing.value) {
        router.put(route('admin.study-programs.update', { id: editingId.value }), form.value, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => closeModal(),
        });
    } else {
        router.post(route('admin.study-programs.store'), form.value, {
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
    router.delete(route('admin.study-programs.destroy', { id: deletingId.value }), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            deleteConfirmOpen.value = false;
            deletingId.value = null;
        },
    });
}

function degreeLabel(level) {
    const map = { d3: 'D3', d4: 'D4', s1: 'S1', s2: 'S2', s3: 'S3', profesi: 'Profesi' };
    return map[level] ?? level;
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Program Studi" />

        <PageHeader title="Program Studi" subtitle="Kelola data program studi." />

        <div v-if="page.props.flash?.success" class="mb-4 rounded-md bg-emerald-50 p-3 text-sm text-emerald-700">
            {{ page.props.flash.success }}
        </div>

        <Card>
            <template #header>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="font-semibold text-content">Daftar Program Studi</h2>
                        <p class="text-sm text-muted">Semua program studi yang terdaftar.</p>
                    </div>
                    <Button variant="primary" size="sm" @click="openCreateModal">
                        ➕ Tambah Prodi
                    </Button>
                </div>
            </template>

            <div v-if="study_programs.length === 0" class="py-8">
                <EmptyState title="Belum ada program studi" description="Belum ada program studi yang ditambahkan." />
            </div>

            <Table v-else :columns="columns" :rows="study_programs">
                <template #cell-degree_level="{ row }">
                    <Badge variant="blue">{{ degreeLabel(row.degree_level) }}</Badge>
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
            <template #title>{{ isEditing ? 'Edit Program Studi' : 'Tambah Program Studi' }}</template>

            <div class="space-y-4">
                <Select v-model="form.faculty_id" label="Fakultas" :options="page.props.faculties?.map(f => ({ value: f.id, label: f.name })) ?? []" required />
                <Input v-model="form.code" label="Kode Prodi" placeholder="Contoh: IF" required />
                <Input v-model="form.name" label="Nama Prodi" placeholder="Contoh: Informatika" required />
                <Select v-model="form.degree_level" label="Jenjang" :options="degreeOptions" required />
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
            title="Hapus Program Studi"
            message="Apakah Anda yakin ingin menghapus program studi ini? Tindakan ini tidak dapat dibatalkan."
            confirm-label="Ya, Hapus"
            cancel-label="Batal"
            @confirm="deleteItem"
            @cancel="deleteConfirmOpen = false"
        />
    </AuthenticatedLayout>
</template>
