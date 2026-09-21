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
import Textarea from '@/components/ui/Textarea.vue';
import ConfirmDialog from '@/components/shared/ConfirmDialog.vue';
import EmptyState from '@/components/ui/EmptyState.vue';

const props = defineProps({
    announcements: { type: Array, default: () => [] },
});

const page = usePage();

const modalOpen = ref(false);
const isEditing = ref(false);
const editingId = ref(null);
const form = ref({
    title: '',
    content: '',
    target_role: '',
    published_at: '',
});

const deleteConfirmOpen = ref(false);
const deletingId = ref(null);

const roleOptions = [
    { value: '', label: 'Semua Role' },
    { value: 'super-admin', label: 'Super Admin' },
    { value: 'kaprodi', label: 'Kaprodi' },
    { value: 'dosen', label: 'Dosen' },
    { value: 'mahasiswa', label: 'Mahasiswa' },
    { value: 'pimpinan', label: 'Pimpinan' },
];

const columns = [
    { key: 'title', label: 'Judul' },
    { key: 'target_role', label: 'Target' },
    { key: 'published_at', label: 'Dipublikasikan' },
    { key: 'created_at', label: 'Dibuat' },
    { key: 'action', label: 'Aksi' },
];

function openCreateModal() {
    isEditing.value = false;
    editingId.value = null;
    form.value = { title: '', content: '', target_role: '', published_at: '' };
    modalOpen.value = true;
}

function openEditModal(item) {
    isEditing.value = true;
    editingId.value = item.id;
    form.value = {
        title: item.title,
        content: item.content,
        target_role: item.target_role || '',
        published_at: item.published_at || '',
    };
    modalOpen.value = true;
}

function closeModal() {
    modalOpen.value = false;
    editingId.value = null;
}

function save() {
    if (isEditing.value) {
        router.put(route('admin.announcements.update', { id: editingId.value }), form.value, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => closeModal(),
        });
    } else {
        router.post(route('admin.announcements.store'), form.value, {
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
    router.delete(route('admin.announcements.destroy', { id: deletingId.value }), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            deleteConfirmOpen.value = false;
            deletingId.value = null;
        },
    });
}

function targetLabel(role) {
    if (!role) return 'Semua Role';
    const map = { 'super-admin': 'Super Admin', kaprodi: 'Kaprodi', dosen: 'Dosen', mahasiswa: 'Mahasiswa', pimpinan: 'Pimpinan' };
    return map[role] ?? role;
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Pengumuman" />

        <PageHeader title="Pengumuman" subtitle="Kelola pengumuman sistem." />

        <div v-if="page.props.flash?.success" class="mb-4 rounded-md bg-emerald-50 p-3 text-sm text-emerald-700">
            {{ page.props.flash.success }}
        </div>

        <Card>
            <template #header>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="font-semibold text-content">Daftar Pengumuman</h2>
                        <p class="text-sm text-muted">Semua pengumuman yang telah dibuat.</p>
                    </div>
                    <Button variant="primary" size="sm" @click="openCreateModal">
                        ➕ Tambah Pengumuman
                    </Button>
                </div>
            </template>

            <div v-if="announcements.length === 0" class="py-8">
                <EmptyState title="Belum ada pengumuman" description="Belum ada pengumuman yang dibuat." />
            </div>

            <Table v-else :columns="columns" :rows="announcements">
                <template #cell-target_role="{ row }">
                    <Badge variant="purple">{{ targetLabel(row.target_role) }}</Badge>
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
            <template #title>{{ isEditing ? 'Edit Pengumuman' : 'Tambah Pengumuman' }}</template>

            <div class="space-y-4">
                <Input v-model="form.title" label="Judul" placeholder="Judul pengumuman" required />
                <Textarea v-model="form.content" label="Konten" placeholder="Isi pengumuman..." rows="5" required />
                <Select v-model="form.target_role" label="Target Role" :options="roleOptions" />
                <Input v-model="form.published_at" label="Tanggal Publikasi" type="datetime-local" />
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
            title="Hapus Pengumuman"
            message="Apakah Anda yakin ingin menghapus pengumuman ini? Tindakan ini tidak dapat dibatalkan."
            confirm-label="Ya, Hapus"
            cancel-label="Batal"
            @confirm="deleteItem"
            @cancel="deleteConfirmOpen = false"
        />
    </AuthenticatedLayout>
</template>
