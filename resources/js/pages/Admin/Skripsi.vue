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
    theses: { type: Object, required: true },
});

const page = usePage();

const filters = ref({
    study_program_id: '',
    status: '',
});

const applyFilters = () => {
    router.get(route('admin.skripsi.index'), filters.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilters = () => {
    filters.value = { study_program_id: '', status: '' };
    router.get(route('admin.skripsi.index'), {}, {
        preserveState: true,
        preserveScroll: true,
    });
};

const statusBadge = (status) => {
    const map = {
        proposal: 'gray',
        seminar_proposal: 'indigo',
        sidang: 'amber',
        lulus: 'emerald',
        revisi: 'rose',
    };
    return map[status] || 'gray';
};

const statusLabel = (status) => {
    const map = {
        proposal: 'Proposal',
        seminar_proposal: 'Seminar Proposal',
        sidang: 'Sidang',
        lulus: 'Lulus',
        revisi: 'Revisi',
    };
    return map[status] || status;
};

// Modal assign pembimbing
const showAssignModal = ref(false);
const selectedThesis = ref(null);
const assignForm = ref({
    supervisor_1_id: '',
    supervisor_2_id: '',
});

const openAssignModal = (thesis) => {
    selectedThesis.value = thesis;
    assignForm.value = {
        supervisor_1_id: thesis.supervisor_1_id || '',
        supervisor_2_id: thesis.supervisor_2_id || '',
    };
    showAssignModal.value = true;
};

const submitAssign = () => {
    router.post(route('admin.skripsi.assign', selectedThesis.value.id), assignForm.value, {
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

const openStatusModal = (thesis) => {
    selectedThesis.value = thesis;
    statusForm.value = { status: thesis.status };
    showStatusModal.value = true;
};

const submitStatus = () => {
    router.put(route('admin.skripsi.update-status', selectedThesis.value.id), statusForm.value, {
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
        <Head title="Manajemen Skripsi" />

        <PageHeader title="Manajemen Skripsi" subtitle="Kelola skripsi seluruh mahasiswa." />

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
                        { value: 'proposal', label: 'Proposal' },
                        { value: 'seminar_proposal', label: 'Seminar Proposal' },
                        { value: 'sidang', label: 'Sidang' },
                        { value: 'lulus', label: 'Lulus' },
                        { value: 'revisi', label: 'Revisi' },
                    ]"
                    placeholder="Semua Status"
                />
                <div class="flex items-end gap-2">
                    <Button variant="primary" @click="applyFilters">Terapkan</Button>
                    <Button variant="secondary" @click="resetFilters">Reset</Button>
                </div>
            </div>
        </Card>

        <!-- Tabel Skripsi -->
        <Card>
            <div v-if="theses.data.length === 0" class="py-6 text-center text-sm text-muted">
                Tidak ada data skripsi.
            </div>

            <div v-else class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wide text-muted">
                            <th class="px-3 py-2">NIM</th>
                            <th class="px-3 py-2">Nama</th>
                            <th class="px-3 py-2">Judul</th>
                            <th class="px-3 py-2">Pembimbing 1</th>
                            <th class="px-3 py-2">Pembimbing 2</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr v-for="thesis in theses.data" :key="thesis.id">
                            <td class="px-3 py-2 font-mono text-content">{{ thesis.student?.nim || '—' }}</td>
                            <td class="px-3 py-2 text-content">{{ thesis.student?.name || '—' }}</td>
                            <td class="max-w-xs truncate px-3 py-2 text-muted">{{ thesis.title }}</td>
                            <td class="px-3 py-2 text-content">{{ thesis.supervisor_one?.user?.name || '—' }}</td>
                            <td class="px-3 py-2 text-content">{{ thesis.supervisor_two?.user?.name || '—' }}</td>
                            <td class="px-3 py-2">
                                <Badge :variant="statusBadge(thesis.status)">{{ statusLabel(thesis.status) }}</Badge>
                            </td>
                            <td class="px-3 py-2">
                                <div class="flex gap-2">
                                    <Button variant="secondary" size="sm" @click="openAssignModal(thesis)">
                                        Assign
                                    </Button>
                                    <Button variant="secondary" size="sm" @click="openStatusModal(thesis)">
                                        Status
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="theses.last_page > 1" class="mt-4 flex items-center justify-between border-t border-border pt-4">
                <div class="text-xs text-muted">
                    Halaman {{ theses.current_page }} dari {{ theses.last_page }}
                </div>
                <div class="flex gap-2">
                    <Link
                        v-if="theses.prev_page_url"
                        :href="theses.prev_page_url"
                        class="rounded border border-border px-3 py-1 text-sm hover:bg-surface"
                    >
                        Sebelumnya
                    </Link>
                    <Link
                        v-if="theses.next_page_url"
                        :href="theses.next_page_url"
                        class="rounded border border-border px-3 py-1 text-sm hover:bg-surface"
                    >
                        Selanjutnya
                    </Link>
                </div>
            </div>
        </Card>

        <!-- Modal Assign Pembimbing -->
        <Modal :open="showAssignModal" title="Assign Pembimbing Skripsi" @close="showAssignModal = false">
            <form @submit.prevent="submitAssign" class="space-y-4">
                <Select
                    v-model="assignForm.supervisor_1_id"
                    label="Pembimbing 1"
                    :options="[]"
                    placeholder="Pilih Dosen"
                    required
                />
                <Select
                    v-model="assignForm.supervisor_2_id"
                    label="Pembimbing 2 (opsional)"
                    :options="[]"
                    placeholder="Pilih Dosen"
                />
                <div class="flex justify-end gap-2">
                    <Button variant="secondary" type="button" @click="showAssignModal = false">Batal</Button>
                    <Button variant="primary" type="submit">Simpan</Button>
                </div>
            </form>
        </Modal>

        <!-- Modal Update Status -->
        <Modal :open="showStatusModal" title="Update Status Skripsi" @close="showStatusModal = false">
            <form @submit.prevent="submitStatus" class="space-y-4">
                <Select
                    v-model="statusForm.status"
                    label="Status"
                    :options="[
                        { value: 'proposal', label: 'Proposal' },
                        { value: 'seminar_proposal', label: 'Seminar Proposal' },
                        { value: 'sidang', label: 'Sidang' },
                        { value: 'lulus', label: 'Lulus' },
                        { value: 'revisi', label: 'Revisi' },
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
