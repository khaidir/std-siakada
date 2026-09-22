<script setup>
import { ref, computed } from 'vue';
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
    users: { type: Array, default: () => [] },
});

const page = usePage();

const modalOpen = ref(false);
const isEditing = ref(false);
const editingId = ref(null);
const form = ref({
    name: '',
    email: '',
    password: '',
    role: 'mahasiswa',
    nim: '',
    study_program_id: null,
    entry_year: '',
    nidn: '',
    academic_rank: '',
});

const deleteConfirmOpen = ref(false);
const deletingId = ref(null);

const roleOptions = [
    { value: 'super-admin', label: 'Super Admin' },
    { value: 'kaprodi', label: 'Kaprodi' },
    { value: 'dosen', label: 'Dosen' },
    { value: 'mahasiswa', label: 'Mahasiswa' },
    { value: 'pimpinan', label: 'Pimpinan' },
];

const academicRankOptions = [
    { value: 'asisten_ahli', label: 'Asisten Ahli' },
    { value: 'lektor', label: 'Lektor' },
    { value: 'lektor_kepala', label: 'Lektor Kepala' },
    { value: 'guru_besar', label: 'Guru Besar' },
];

const columns = [
    { key: 'name', label: 'Nama' },
    { key: 'email', label: 'Email' },
    { key: 'role', label: 'Role' },
    { key: 'created_at', label: 'Dibuat' },
    { key: 'action', label: 'Aksi' },
];

const showDosenFields = computed(() => form.value.role === 'dosen');
const showMahasiswaFields = computed(() => form.value.role === 'mahasiswa');

function openCreateModal() {
    isEditing.value = false;
    editingId.value = null;
    form.value = {
        name: '',
        email: '',
        password: '',
        role: 'mahasiswa',
        nim: '',
        study_program_id: null,
        entry_year: '',
        nidn: '',
        academic_rank: '',
    };
    modalOpen.value = true;
}

function openEditModal(user) {
    isEditing.value = true;
    editingId.value = user.id;
    form.value = {
        name: user.name,
        email: user.email,
        password: '',
        role: user.role,
        nim: user.nim || '',
        study_program_id: user.study_program_id || null,
        entry_year: user.entry_year || '',
        nidn: user.nidn || '',
        academic_rank: user.academic_rank || '',
    };
    modalOpen.value = true;
}

function closeModal() {
    modalOpen.value = false;
    editingId.value = null;
}

function save() {
    const data = { ...form.value };
    if (isEditing.value && !data.password) {
        delete data.password;
    }

    if (isEditing.value) {
        router.put(route('admin.users.update', { id: editingId.value }), data, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => closeModal(),
        });
    } else {
        router.post(route('admin.users.store'), data, {
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

function deleteUser() {
    router.delete(route('admin.users.destroy', { id: deletingId.value }), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            deleteConfirmOpen.value = false;
            deletingId.value = null;
        },
    });
}

function roleBadgeVariant(role) {
    const map = {
        'super-admin': 'rose',
        'kaprodi': 'blue',
        'dosen': 'emerald',
        'mahasiswa': 'amber',
        'pimpinan': 'purple',
    };
    return map[role] ?? 'slate';
}

function roleLabel(role) {
    const map = {
        'super-admin': 'Super Admin',
        'kaprodi': 'Kaprodi',
        'dosen': 'Dosen',
        'mahasiswa': 'Mahasiswa',
        'pimpinan': 'Pimpinan',
    };
    return map[role] ?? role;
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Pengguna" />

        <PageHeader title="Pengguna" subtitle="Kelola pengguna, role, dan akses sistem." />

        <div v-if="page.props.flash?.success" class="mb-4 rounded-md bg-success-subtle p-3 text-sm text-success-strong">
            {{ page.props.flash.success }}
        </div>

        <div v-if="page.props.errors?.length" class="mb-4 rounded-md bg-danger-subtle p-3 text-sm text-danger-strong">
            <p v-for="(err, i) in page.props.errors" :key="i">{{ err }}</p>
        </div>

        <Card>
            <template #header>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="font-semibold text-content">Daftar Pengguna</h2>
                        <p class="text-sm text-muted">Semua pengguna yang terdaftar di sistem.</p>
                    </div>
                    <Button variant="primary" size="sm" @click="openCreateModal">
                        ➕ Tambah Pengguna
                    </Button>
                </div>
            </template>

            <div v-if="users.length === 0" class="py-8">
                <EmptyState title="Belum ada pengguna" description="Belum ada pengguna yang terdaftar." />
            </div>

            <Table v-else :columns="columns" :rows="users">
                <template #cell-role="{ row }">
                    <Badge :variant="roleBadgeVariant(row.role)">
                        {{ roleLabel(row.role) }}
                    </Badge>
                </template>
                <template #cell-action="{ row }">
                    <div class="flex gap-2">
                        <Button variant="secondary" size="xs" @click="openEditModal(row)">
                            Edit
                        </Button>
                        <Button variant="danger" size="xs" @click="confirmDelete(row.id)">
                            Hapus
                        </Button>
                    </div>
                </template>
            </Table>
        </Card>

        <!-- Modal Form -->
        <Modal :open="modalOpen" @close="closeModal">
            <template #title>
                {{ isEditing ? 'Edit Pengguna' : 'Tambah Pengguna' }}
            </template>

            <form @submit.prevent="save" class="space-y-4">
                <Input
                    v-model="form.name"
                    label="Nama"
                    placeholder="Nama lengkap"
                    :error="page.props.errors?.name"
                    required
                />

                <Input
                    v-model="form.email"
                    label="Email"
                    type="email"
                    placeholder="email@example.com"
                    :error="page.props.errors?.email"
                    required
                />

                <Input
                    v-model="form.password"
                    :label="isEditing ? 'Password (kosongkan jika tidak diubah)' : 'Password'"
                    type="password"
                    placeholder="Minimal 8 karakter"
                    :error="page.props.errors?.password"
                    :required="!isEditing"
                />

                <Select
                    v-model="form.role"
                    label="Role"
                    :options="roleOptions"
                    :error="page.props.errors?.role"
                    required
                />

                    <!-- Field untuk Dosen -->
                <template v-if="showDosenFields">
                    <Input
                        v-model="form.nidn"
                        label="NIDN"
                        placeholder="Nomor Induk Dosen Nasional"
                        :error="page.props.errors?.nidn"
                        required
                    />

                    <Select
                        v-model="form.study_program_id"
                        label="Program Studi"
                        :options="(page.props.study_programs ?? []).map(sp => ({ value: sp.id, label: sp.name }))"
                        :error="page.props.errors?.study_program_id"
                        required
                    />

                    <Select
                        v-model="form.academic_rank"
                        label="Pangkat Akademik"
                        :options="academicRankOptions"
                        :error="page.props.errors?.academic_rank"
                        required
                    />
                </template>

                <!-- Field untuk Mahasiswa -->
                <template v-if="showMahasiswaFields">
                    <Input
                        v-model="form.nim"
                        label="NIM"
                        placeholder="Nomor Induk Mahasiswa"
                        :error="page.props.errors?.nim"
                        required
                    />

                    <Select
                        v-model="form.study_program_id"
                        label="Program Studi"
                        :options="(page.props.study_programs ?? []).map(sp => ({ value: sp.id, label: sp.name }))"
                        :error="page.props.errors?.study_program_id"
                        required
                    />

                    <Input
                        v-model="form.entry_year"
                        label="Angkatan"
                        placeholder="Contoh: 2024"
                        :error="page.props.errors?.entry_year"
                        required
                    />
                </template>

                <div class="flex justify-end gap-3 pt-4">
                    <Button variant="secondary" type="button" @click="closeModal">
                        Batal
                    </Button>
                    <Button variant="primary" type="submit">
                        {{ isEditing ? 'Simpan Perubahan' : 'Tambah Pengguna' }}
                    </Button>
                </div>
            </form>
        </Modal>

        <!-- Confirm Delete -->
        <ConfirmDialog
            :open="deleteConfirmOpen"
            title="Hapus Pengguna"
            message="Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan."
            @confirm="deleteUser"
            @cancel="deleteConfirmOpen = false"
        />
    </AuthenticatedLayout>
</template>
