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
    academic_years: { type: Array, default: () => [] },
});

const page = usePage();

// --- Academic Year Modal ---
const ayModalOpen = ref(false);
const ayEditing = ref(false);
const ayEditingId = ref(null);
const ayForm = ref({ code: '', name: '', start_date: '', end_date: '', is_active: false });

const ayDeleteConfirmOpen = ref(false);
const ayDeletingId = ref(null);

// --- Semester Modal ---
const semModalOpen = ref(false);
const semEditing = ref(false);
const semEditingId = ref(null);
const semForm = ref({ academic_year_id: null, type: 'ganjil', start_date: '', end_date: '', is_active: false });

const semDeleteConfirmOpen = ref(false);
const semDeletingId = ref(null);

const semesterTypeOptions = [
    { value: 'ganjil', label: 'Ganjil' },
    { value: 'genap', label: 'Genap' },
    { value: 'pendek', label: 'Pendek' },
];

const ayColumns = [
    { key: 'code', label: 'Kode' },
    { key: 'name', label: 'Nama' },
    { key: 'start_date', label: 'Mulai' },
    { key: 'end_date', label: 'Selesai' },
    { key: 'is_active', label: 'Aktif' },
    { key: 'action', label: 'Aksi' },
];

const semColumns = [
    { key: 'type', label: 'Semester' },
    { key: 'start_date', label: 'Mulai' },
    { key: 'end_date', label: 'Selesai' },
    { key: 'is_active', label: 'Aktif' },
    { key: 'action', label: 'Aksi' },
];

// --- Academic Year ---
function openCreateAY() {
    ayEditing.value = false;
    ayEditingId.value = null;
    ayForm.value = { code: '', name: '', start_date: '', end_date: '', is_active: false };
    ayModalOpen.value = true;
}

function openEditAY(ay) {
    ayEditing.value = true;
    ayEditingId.value = ay.id;
    ayForm.value = {
        code: ay.code,
        name: ay.name,
        start_date: ay.start_date,
        end_date: ay.end_date,
        is_active: ay.is_active,
    };
    ayModalOpen.value = true;
}

function closeAYModal() {
    ayModalOpen.value = false;
    ayEditingId.value = null;
}

function saveAY() {
    if (ayEditing.value) {
        router.put(route('admin.periods.academic-year.update', { id: ayEditingId.value }), ayForm.value, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => closeAYModal(),
        });
    } else {
        router.post(route('admin.periods.academic-year.store'), ayForm.value, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => closeAYModal(),
        });
    }
}

function confirmDeleteAY(id) {
    ayDeletingId.value = id;
    ayDeleteConfirmOpen.value = true;
}

function deleteAY() {
    router.delete(route('admin.periods.academic-year.destroy', { id: ayDeletingId.value }), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            ayDeleteConfirmOpen.value = false;
            ayDeletingId.value = null;
        },
    });
}

// --- Semester ---
function openCreateSem(academicYearId) {
    semEditing.value = false;
    semEditingId.value = null;
    semForm.value = { academic_year_id: academicYearId, type: 'ganjil', start_date: '', end_date: '', is_active: false };
    semModalOpen.value = true;
}

function openEditSem(sem) {
    semEditing.value = true;
    semEditingId.value = sem.id;
    semForm.value = {
        academic_year_id: sem.academic_year_id,
        type: sem.type,
        start_date: sem.start_date,
        end_date: sem.end_date,
        is_active: sem.is_active,
    };
    semModalOpen.value = true;
}

function closeSemModal() {
    semModalOpen.value = false;
    semEditingId.value = null;
}

function saveSem() {
    if (semEditing.value) {
        router.put(route('admin.periods.semester.update', { id: semEditingId.value }), semForm.value, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => closeSemModal(),
        });
    } else {
        router.post(route('admin.periods.semester.store'), semForm.value, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => closeSemModal(),
        });
    }
}

function confirmDeleteSem(id) {
    semDeletingId.value = id;
    semDeleteConfirmOpen.value = true;
}

function deleteSem() {
    router.delete(route('admin.periods.semester.destroy', { id: semDeletingId.value }), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            semDeleteConfirmOpen.value = false;
            semDeletingId.value = null;
        },
    });
}

function semesterTypeLabel(type) {
    const map = { ganjil: 'Ganjil', genap: 'Genap', pendek: 'Pendek' };
    return map[type] ?? type;
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Periode Akademik" />

        <PageHeader title="Periode Akademik" subtitle="Kelola tahun ajaran dan semester." />

        <div v-if="page.props.flash?.success" class="mb-4 rounded-md bg-emerald-50 p-3 text-sm text-emerald-700">
            {{ page.props.flash.success }}
        </div>

        <div v-if="academic_years.length === 0" class="py-8">
            <EmptyState title="Belum ada periode akademik" description="Belum ada tahun ajaran yang ditambahkan." />
        </div>

        <div v-for="ay in academic_years" :key="ay.id" class="mb-6">
            <Card>
                <template #header>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <h2 class="font-semibold text-content">{{ ay.code }} — {{ ay.name }}</h2>
                            <Badge v-if="ay.is_active" variant="emerald">Aktif</Badge>
                        </div>
                        <div class="flex gap-2">
                            <Button variant="outline" size="sm" @click="openEditAY(ay)">✏️ Edit TA</Button>
                            <Button variant="danger" size="sm" @click="confirmDeleteAY(ay.id)">🗑️ Hapus TA</Button>
                            <Button variant="primary" size="sm" @click="openCreateSem(ay.id)">➕ Tambah Semester</Button>
                        </div>
                    </div>
                </template>

                <div class="mb-3 text-sm text-muted">
                    {{ ay.start_date }} — {{ ay.end_date }}
                </div>

                <div v-if="ay.semesters.length === 0">
                    <p class="text-sm text-muted">Belum ada semester.</p>
                </div>

                <Table v-else :columns="semColumns" :rows="ay.semesters">
                    <template #cell-type="{ row }">
                        <Badge variant="blue">{{ semesterTypeLabel(row.type) }}</Badge>
                    </template>
                    <template #cell-is_active="{ row }">
                        <Badge v-if="row.is_active" variant="emerald">Aktif</Badge>
                        <Badge v-else variant="slate">Tidak</Badge>
                    </template>
                    <template #cell-action="{ row }">
                        <div class="flex gap-2">
                            <Button variant="outline" size="sm" @click="openEditSem(row)">✏️ Edit</Button>
                            <Button variant="danger" size="sm" @click="confirmDeleteSem(row.id)">🗑️ Hapus</Button>
                        </div>
                    </template>
                </Table>
            </Card>
        </div>

        <div class="mb-6">
            <Button variant="primary" @click="openCreateAY">➕ Tambah Tahun Ajaran</Button>
        </div>

        <!-- Academic Year Modal -->
        <Modal :open="ayModalOpen" @close="closeAYModal">
            <template #title>{{ ayEditing ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran' }}</template>

            <div class="space-y-4">
                <Input v-model="ayForm.code" label="Kode" placeholder="Contoh: 2024/2025" required />
                <Input v-model="ayForm.name" label="Nama" placeholder="Contoh: Tahun Akademik 2024/2025" required />
                <div class="grid grid-cols-2 gap-4">
                    <Input v-model="ayForm.start_date" label="Tanggal Mulai" type="date" required />
                    <Input v-model="ayForm.end_date" label="Tanggal Selesai" type="date" required />
                </div>
                <label class="flex items-center gap-2 text-sm">
                    <input v-model="ayForm.is_active" type="checkbox" class="rounded border-gray-300" />
                    Aktifkan sebagai tahun ajaran berjalan
                </label>
            </div>

            <template #footer>
                <div class="flex justify-end gap-3">
                    <Button variant="outline" @click="closeAYModal">Batal</Button>
                    <Button variant="primary" @click="saveAY">{{ ayEditing ? 'Simpan Perubahan' : 'Tambah' }}</Button>
                </div>
            </template>
        </Modal>

        <!-- Semester Modal -->
        <Modal :open="semModalOpen" @close="closeSemModal">
            <template #title>{{ semEditing ? 'Edit Semester' : 'Tambah Semester' }}</template>

            <div class="space-y-4">
                <Select v-model="semForm.type" label="Tipe Semester" :options="semesterTypeOptions" required />
                <div class="grid grid-cols-2 gap-4">
                    <Input v-model="semForm.start_date" label="Tanggal Mulai" type="date" required />
                    <Input v-model="semForm.end_date" label="Tanggal Selesai" type="date" required />
                </div>
                <label class="flex items-center gap-2 text-sm">
                    <input v-model="semForm.is_active" type="checkbox" class="rounded border-gray-300" />
                    Aktifkan sebagai semester berjalan
                </label>
            </div>

            <template #footer>
                <div class="flex justify-end gap-3">
                    <Button variant="outline" @click="closeSemModal">Batal</Button>
                    <Button variant="primary" @click="saveSem">{{ semEditing ? 'Simpan Perubahan' : 'Tambah' }}</Button>
                </div>
            </template>
        </Modal>

        <!-- Confirm Delete AY -->
        <ConfirmDialog
            :open="ayDeleteConfirmOpen"
            title="Hapus Tahun Ajaran"
            message="Apakah Anda yakin ingin menghapus tahun ajaran ini? Tindakan ini tidak dapat dibatalkan."
            confirm-label="Ya, Hapus"
            cancel-label="Batal"
            @confirm="deleteAY"
            @cancel="ayDeleteConfirmOpen = false"
        />

        <!-- Confirm Delete Semester -->
        <ConfirmDialog
            :open="semDeleteConfirmOpen"
            title="Hapus Semester"
            message="Apakah Anda yakin ingin menghapus semester ini? Tindakan ini tidak dapat dibatalkan."
            confirm-label="Ya, Hapus"
            cancel-label="Batal"
            @confirm="deleteSem"
            @cancel="semDeleteConfirmOpen = false"
        />
    </AuthenticatedLayout>
</template>
