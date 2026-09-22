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
    offerings: { type: Array, default: () => [] },
});

const page = usePage();

const modalOpen = ref(false);
const isEditing = ref(false);
const editingId = ref(null);
const form = ref({
    course_id: null,
    semester_id: null,
    lecturer_id: null,
    classroom_id: null,
    day: 'senin',
    start_time: '08:00',
    end_time: '09:40',
    quota: 40,
});

const deleteConfirmOpen = ref(false);
const deletingId = ref(null);

const dayOptions = [
    { value: 'senin', label: 'Senin' },
    { value: 'selasa', label: 'Selasa' },
    { value: 'rabu', label: 'Rabu' },
    { value: 'kamis', label: 'Kamis' },
    { value: 'jumat', label: 'Jumat' },
    { value: 'sabtu', label: 'Sabtu' },
    { value: 'minggu', label: 'Minggu' },
];

const columns = [
    { key: 'course_code', label: 'Kode MK' },
    { key: 'course_name', label: 'Mata Kuliah' },
    { key: 'semester_label', label: 'Semester' },
    { key: 'lecturer_name', label: 'Dosen' },
    { key: 'classroom_name', label: 'Ruangan' },
    { key: 'day', label: 'Hari' },
    { key: 'start_time', label: 'Mulai' },
    { key: 'end_time', label: 'Selesai' },
    { key: 'quota', label: 'Kuota' },
    { key: 'action', label: 'Aksi' },
];

function openCreateModal() {
    isEditing.value = false;
    editingId.value = null;
    form.value = {
        course_id: null,
        semester_id: null,
        lecturer_id: null,
        classroom_id: null,
        day: 'senin',
        start_time: '08:00',
        end_time: '09:40',
        quota: 40,
    };
    modalOpen.value = true;
}

function openEditModal(o) {
    isEditing.value = true;
    editingId.value = o.id;
    form.value = {
        course_id: o.course_id,
        semester_id: o.semester_id,
        lecturer_id: o.lecturer_id,
        classroom_id: o.classroom_id,
        day: o.day,
        start_time: o.start_time,
        end_time: o.end_time,
        quota: o.quota,
    };
    modalOpen.value = true;
}

function closeModal() {
    modalOpen.value = false;
    editingId.value = null;
}

function save() {
    if (isEditing.value) {
        router.put(route('admin.course-offerings.update', { id: editingId.value }), form.value, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => closeModal(),
        });
    } else {
        router.post(route('admin.course-offerings.store'), form.value, {
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
    router.delete(route('admin.course-offerings.destroy', { id: deletingId.value }), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            deleteConfirmOpen.value = false;
            deletingId.value = null;
        },
    });
}

function dayLabel(d) {
    const map = { senin: 'Senin', selasa: 'Selasa', rabu: 'Rabu', kamis: 'Kamis', jumat: 'Jumat', sabtu: 'Sabtu', minggu: 'Minggu' };
    return map[d] ?? d;
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Kelas & Jadwal" />

        <PageHeader title="Kelas & Jadwal" subtitle="Kelola kelas perkuliahan dan jadwal." />

        <div v-if="page.props.flash?.success" class="mb-4 rounded-md bg-success-subtle p-3 text-sm text-success-strong">
            {{ page.props.flash.success }}
        </div>

        <Card>
            <template #header>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="font-semibold text-content">Daftar Kelas & Jadwal</h2>
                        <p class="text-sm text-muted">Semua kelas perkuliahan yang berjalan.</p>
                    </div>
                    <Button variant="primary" size="sm" @click="openCreateModal">
                        ➕ Tambah Kelas
                    </Button>
                </div>
            </template>

            <div v-if="offerings.length === 0" class="py-8">
                <EmptyState title="Belum ada kelas" description="Belum ada kelas perkuliahan yang ditambahkan." />
            </div>

            <Table v-else :columns="columns" :rows="offerings">
                <template #cell-day="{ row }">
                    <Badge variant="blue">{{ dayLabel(row.day) }}</Badge>
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
            <template #title>{{ isEditing ? 'Edit Kelas' : 'Tambah Kelas' }}</template>

            <div class="space-y-4">
                <Select v-model="form.course_id" label="Mata Kuliah" :options="page.props.courses?.map(c => ({ value: c.id, label: `[${c.code}] ${c.name}` })) ?? []" required />
                <Select v-model="form.semester_id" label="Semester" :options="page.props.semesters?.map(s => ({ value: s.id, label: s.label })) ?? []" required />
                <Select v-model="form.lecturer_id" label="Dosen" :options="page.props.lecturers?.map(l => ({ value: l.id, label: l.name })) ?? []" required />
                <Select v-model="form.classroom_id" label="Ruangan" :options="page.props.classrooms?.map(r => ({ value: r.id, label: `${r.code} - ${r.name}` })) ?? []" required />
                <Select v-model="form.day" label="Hari" :options="dayOptions" required />
                <div class="grid grid-cols-2 gap-4">
                    <Input v-model="form.start_time" label="Jam Mulai" type="time" required />
                    <Input v-model="form.end_time" label="Jam Selesai" type="time" required />
                </div>
                <Input v-model.number="form.quota" label="Kuota" type="number" min="1" max="999" required />
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
            title="Hapus Kelas"
            message="Apakah Anda yakin ingin menghapus kelas ini? Tindakan ini tidak dapat dibatalkan."
            confirm-label="Ya, Hapus"
            cancel-label="Batal"
            @confirm="deleteItem"
            @cancel="deleteConfirmOpen = false"
        />
    </AuthenticatedLayout>
</template>
