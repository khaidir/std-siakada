<script setup>
import { ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import Card from '@/components/ui/Card.vue';
import Button from '@/components/ui/Button.vue';
import Select from '@/components/ui/Select.vue';
import Badge from '@/components/ui/Badge.vue';
import Modal from '@/components/ui/Modal.vue';

const props = defineProps({
    internships: { type: Object, required: true },
});

const page = usePage();

const filters = ref({
    study_program_id: '',
    status: '',
});

const applyFilters = () => {
    router.get(route('admin.kp.index'), filters.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilters = () => {
    filters.value = { study_program_id: '', status: '' };
    router.get(route('admin.kp.index'), {}, {
        preserveState: true,
        preserveScroll: true,
    });
};

const statusBadge = (status) => {
    const map = {
        draft: 'gray',
        berjalan: 'indigo',
        selesai: 'emerald',
        ditolak: 'rose',
    };
    return map[status] || 'gray';
};

const statusLabel = (status) => {
    const map = {
        draft: 'Draft',
        berjalan: 'Berjalan',
        selesai: 'Selesai',
        ditolak: 'Ditolak',
    };
    return map[status] || status;
};

// Modal assign pembimbing
const showAssignModal = ref(false);
const selectedInternship = ref(null);
const assignForm = ref({ supervisor_id: '' });

const openAssignModal = (internship) => {
    selectedInternship.value = internship;
    assignForm.value = { supervisor_id: internship.supervisor_id || '' };
    showAssignModal.value = true;
};

const submitAssign = () => {
    router.post(route('admin.kp.assign', selectedInternship.value.id), assignForm.value, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            showAssignModal.value = false;
        },
    });
};

// Modal update status
const showStatusModal = ref(false);
const statusForm = ref({ status: '' });

const openStatusModal = (internship) => {
    selectedInternship.value = internship;
    statusForm.value = { status: internship.status };
    showStatusModal.value = true;
};

const submitStatus = () => {
    router.put(route('admin.kp.update-status', selectedInternship.value.id), statusForm.value, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            showStatusModal.value = false;
        },
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Manajemen KP" />

        <PageHeader title="Manajemen KP" subtitle="Kelola Kerja Praktik seluruh mahasiswa." />

        <!-- Filter -->
        <Card class="mb-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                <Select
                    v-model="filters.study_program_id"
                    label="Program Studi"
                    :options="[]"
                    placeholder="Semua Prodi"
                />
                <Select
                    v-model="filters.status"
                    label="Status"
                    :options="[
                        { value: 'draft', label: 'Draft' },
                        { value: 'berjalan', label: 'Berjalan' },
                        { value: 'selesai', label: 'Selesai' },
                        { value: 'ditolak', label: 'Ditolak' },
                    ]"
                    placeholder="Semua Status"
                />
                <div class="flex items-end gap-2">
                    <Button variant="primary" @click="applyFilters">Terapkan</Button>
                    <Button variant="secondary" @click="resetFilters">Reset</Button>
                </div>
            </div>
        </Card>

        <!-- Tabel KP -->
        <Card>
            <div v-if="internships.data.length === 0" class="py-6 text-center text-sm text-muted">
                Tidak ada data KP.
            </div>

            <div v-else class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wide text-muted">
                            <th class="px-3 py-2">NIM</th>
                            <th class="px-3 py-2">Nama</th>
                            <th class="px-3 py-2">Perusahaan</th>
                            <th class="px-3 py-2">Dosen Pembimbing</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr v-for="internship in internships.data" :key="internship.id">
                            <td class="px-3 py-2 font-mono text-content">{{ internship.student?.nim || '—' }}</td>
                            <td class="px-3 py-2 text-content">{{ internship.student?.name || '—' }}</td>
                            <td class="px-3 py-2 text-muted">{{ internship.company_name || '—' }}</td>
                            <td class="px-3 py-2 text-content">{{ internship.supervisor?.user?.name || '—' }}</td>
                            <td class="px-3 py-2">
                                <Badge :variant="statusBadge(internship.status)">{{ statusLabel(internship.status) }}</Badge>
                            </td>
                            <td class="px-3 py-2">
                                <div class="flex gap-2">
                                    <Button variant="secondary" size="sm" @click="openAssignModal(internship)">
                                        Assign
                                    </Button>
                                    <Button variant="secondary" size="sm" @click="openStatusModal(internship)">
                                        Status
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="internships.last_page > 1" class="mt-4 flex items-center justify-between border-t border-border pt-4">
                <div class="text-xs text-muted">
                    Halaman {{ internships.current_page }} dari {{ internships.last_page }}
                </div>
                <div class="flex gap-2">
                    <Link
                        v-if="internships.prev_page_url"
                        :href="internships.prev_page_url"
                        class="rounded border border-border px-3 py-1 text-sm hover:bg-surface"
                    >
                        Sebelumnya
                    </Link>
                    <Link
                        v-if="internships.next_page_url"
                        :href="internships.next_page_url"
                        class="rounded border border-border px-3 py-1 text-sm hover:bg-surface"
                    >
                        Selanjutnya
                    </Link>
                </div>
            </div>
        </Card>

        <!-- Modal Assign Pembimbing -->
        <Modal :open="showAssignModal" title="Assign Pembimbing KP" @close="showAssignModal = false">
            <form @submit.prevent="submitAssign" class="space-y-4">
                <Select
                    v-model="assignForm.supervisor_id"
                    label="Dosen Pembimbing"
                    :options="[]"
                    placeholder="Pilih Dosen"
                    required
                />
                <div class="flex justify-end gap-2">
                    <Button variant="secondary" type="button" @click="showAssignModal = false">Batal</Button>
                    <Button variant="primary" type="submit">Simpan</Button>
                </div>
            </form>
        </Modal>

        <!-- Modal Update Status -->
        <Modal :open="showStatusModal" title="Update Status KP" @close="showStatusModal = false">
            <form @submit.prevent="submitStatus" class="space-y-4">
                <Select
                    v-model="statusForm.status"
                    label="Status"
                    :options="[
                        { value: 'draft', label: 'Draft' },
                        { value: 'berjalan', label: 'Berjalan' },
                        { value: 'selesai', label: 'Selesai' },
                        { value: 'ditolak', label: 'Ditolak' },
                    ]"
                    required
                />
                <div class="flex justify-end gap-2">
                    <Button variant="secondary" type="button" @click="showStatusModal = false">Batal</Button>
                    <Button variant="primary" type="submit">Simpan</Button>
                </div>
            </form>
        </Modal>
    </AuthenticatedLayout>
</template>
