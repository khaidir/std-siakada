<script setup>
import { ref } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import Card from '@/components/ui/Card.vue';
import Button from '@/components/ui/Button.vue';
import Table from '@/components/ui/Table.vue';
import Modal from '@/components/ui/Modal.vue';
import Input from '@/components/ui/Input.vue';
import ConfirmDialog from '@/components/shared/ConfirmDialog.vue';
import EmptyState from '@/components/ui/EmptyState.vue';

const props = defineProps({
    faculties: { type: Array, default: () => [] },
});

const page = usePage();

const modalOpen = ref(false);
const isEditing = ref(false);
const editingId = ref(null);
const form = ref({ code: '', name: '' });

const deleteConfirmOpen = ref(false);
const deletingId = ref(null);

const columns = [
    { key: 'code', label: 'Kode' },
    { key: 'name', label: 'Nama' },
    { key: 'action', label: 'Aksi' },
];

function openCreateModal() {
    isEditing.value = false;
    editingId.value = null;
    form.value = { code: '', name: '' };
    modalOpen.value = true;
}

function openEditModal(faculty) {
    isEditing.value = true;
    editingId.value = faculty.id;
    form.value = { code: faculty.code, name: faculty.name };
    modalOpen.value = true;
}

function closeModal() {
    modalOpen.value = false;
    editingId.value = null;
}

function save() {
    if (isEditing.value) {
        router.put(route('admin.faculties.update', { id: editingId.value }), form.value, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => closeModal(),
        });
    } else {
        router.post(route('admin.faculties.store'), form.value, {
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
    router.delete(route('admin.faculties.destroy', { id: deletingId.value }), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            deleteConfirmOpen.value = false;
            deletingId.value = null;
        },
    });
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Fakultas" />

        <PageHeader title="Fakultas" subtitle="Kelola data fakultas." />

        <div v-if="page.props.flash?.success" class="mb-4 rounded-md bg-success-subtle p-3 text-sm text-success-strong">
            {{ page.props.flash.success }}
        </div>

        <Card>
            <template #header>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="font-semibold text-content">Daftar Fakultas</h2>
                        <p class="text-sm text-muted">Semua fakultas yang terdaftar.</p>
                    </div>
                    <Button variant="primary" size="sm" @click="openCreateModal">
                        ➕ Tambah Fakultas
                    </Button>
                </div>
            </template>

            <div v-if="faculties.length === 0" class="py-8">
                <EmptyState title="Belum ada fakultas" description="Belum ada fakultas yang ditambahkan." />
            </div>

            <Table v-else :columns="columns" :rows="faculties">
                <template #cell-action="{ row }">
                    <div class="flex gap-2">
                        <Button variant="outline" size="sm" @click="openEditModal(row)">✏️ Edit</Button>
                        <Button variant="danger" size="sm" @click="confirmDelete(row.id)">🗑️ Hapus</Button>
                    </div>
                </template>
            </Table>
        </Card>

        <Modal :open="modalOpen" @close="closeModal">
            <template #title>{{ isEditing ? 'Edit Fakultas' : 'Tambah Fakultas' }}</template>

            <div class="space-y-4">
                <Input v-model="form.code" label="Kode Fakultas" placeholder="Contoh: FIK" required />
                <Input v-model="form.name" label="Nama Fakultas" placeholder="Contoh: Fakultas Ilmu Komputer" required />
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
            title="Hapus Fakultas"
            message="Apakah Anda yakin ingin menghapus fakultas ini? Tindakan ini tidak dapat dibatalkan."
            confirm-label="Ya, Hapus"
            cancel-label="Batal"
            @confirm="deleteItem"
            @cancel="deleteConfirmOpen = false"
        />
    </AuthenticatedLayout>
</template>
