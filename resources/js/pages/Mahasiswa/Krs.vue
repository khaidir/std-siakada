<script setup>
import { computed, ref } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import Card from '@/components/ui/Card.vue';
import Button from '@/components/ui/Button.vue';
import Badge from '@/components/ui/Badge.vue';
import Table from '@/components/ui/Table.vue';
import ConfirmDialog from '@/components/shared/ConfirmDialog.vue';
import EmptyState from '@/components/ui/EmptyState.vue';

const props = defineProps({
    semester: { type: Object, default: null },
    plan: { type: Object, default: null },
    offerings: { type: Array, default: () => [] },
    selected: { type: Array, default: () => [] },
    sks_limit: { type: Number, default: 0 },
    total_sks: { type: Number, default: 0 },
});

const page = usePage();

const submitConfirmOpen = ref(false);
const removeConfirmOpen = ref(false);
const removingId = ref(null);

const canEdit = computed(() => props.plan?.can_edit ?? false);

const availableColumns = [
    { key: 'course_code', label: 'Kode' },
    { key: 'course_name', label: 'Mata Kuliah' },
    { key: 'sks', label: 'SKS' },
    { key: 'lecturer', label: 'Dosen' },
    { key: 'day', label: 'Hari' },
    { key: 'start_time', label: 'Mulai' },
    { key: 'end_time', label: 'Selesai' },
    { key: 'classroom', label: 'Ruang' },
    { key: 'action', label: 'Aksi' },
];

const selectedColumns = [
    { key: 'course_code', label: 'Kode' },
    { key: 'course_name', label: 'Mata Kuliah' },
    { key: 'sks', label: 'SKS' },
    { key: 'lecturer', label: 'Dosen' },
    { key: 'day', label: 'Hari' },
    { key: 'start_time', label: 'Mulai' },
    { key: 'end_time', label: 'Selesai' },
    { key: 'status', label: 'Status' },
    { key: 'action', label: 'Aksi' },
];

const sksPercentage = computed(() => {
    if (props.sks_limit === 0) return 0;
    return Math.min(100, Math.round((props.total_sks / props.sks_limit) * 100));
});

const sksBarColor = computed(() => {
    if (sksPercentage.value >= 90) return 'bg-rose-500';
    if (sksPercentage.value >= 75) return 'bg-amber-500';
    return 'bg-emerald-500';
});

function addCourse(offeringId) {
    router.post(route('mahasiswa.krs.store'), {
        course_offering_id: offeringId,
    }, {
        preserveScroll: true,
        preserveState: true,
    });
}

function confirmRemove(detailId) {
    removingId.value = detailId;
    removeConfirmOpen.value = true;
}

function removeCourse() {
    router.delete(route('mahasiswa.krs.destroy', { id: removingId.value }), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            removeConfirmOpen.value = false;
            removingId.value = null;
        },
    });
}

function confirmSubmit() {
    submitConfirmOpen.value = true;
}

function submitKrs() {
    router.post(route('mahasiswa.krs.submit'), {}, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            submitConfirmOpen.value = false;
        },
    });
}

function statusBadgeVariant(status) {
    const map = {
        pending: 'amber',
        approved: 'emerald',
        rejected: 'rose',
    };
    return map[status] ?? 'slate';
}

function statusLabel(status) {
    const map = {
        pending: 'Pending',
        approved: 'Disetujui',
        rejected: 'Ditolak',
    };
    return map[status] ?? status;
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="KRS (Pilih Mata Kuliah)" />

        <PageHeader title="Kartu Rencana Studi" subtitle="Pilih mata kuliah yang akan diambil semester ini." />

        <div v-if="page.props.flash?.success" class="mb-4 rounded-md bg-emerald-50 p-3 text-sm text-emerald-700">
            {{ page.props.flash.success }}
        </div>

        <div v-if="page.props.errors?.length" class="mb-4 rounded-md bg-rose-50 p-3 text-sm text-rose-700">
            <p v-for="(err, i) in page.props.errors" :key="i">{{ err }}</p>
        </div>

        <!-- Info Semester -->
        <Card class="mb-6">
            <template #header>
                <h2 class="font-semibold text-content">Informasi Semester</h2>
            </template>
            <div v-if="semester" class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <p class="text-xs text-muted">Semester</p>
                    <p class="font-medium text-content">{{ semester.type === 'ganjil' ? 'Ganjil' : 'Genap' }}</p>
                </div>
                <div>
                    <p class="text-xs text-muted">Tahun Akademik</p>
                    <p class="font-medium text-content">{{ semester.academic_year }}</p>
                </div>
                <div>
                    <p class="text-xs text-muted">Status KRS</p>
                    <Badge v-if="plan" :variant="plan.status === 'approved' ? 'emerald' : plan.status === 'submitted' ? 'amber' : 'slate'">
                        {{ plan.status === 'draft' ? 'Draft' : plan.status === 'submitted' ? 'Menunggu' : plan.status === 'approved' ? 'Disetujui' : 'Ditolak' }}
                    </Badge>
                    <span v-else class="text-xs text-muted">—</span>
                </div>
            </div>
            <div v-else>
                <p class="text-sm text-muted">Tidak ada semester aktif saat ini.</p>
            </div>
        </Card>

        <!-- SKS Progress -->
        <Card class="mb-6">
            <template #header>
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold text-content">Penggunaan SKS</h2>
                    <span class="text-sm font-medium text-content">
                        {{ total_sks }} / {{ sks_limit }} SKS
                    </span>
                </div>
            </template>
            <div class="h-3 w-full overflow-hidden rounded-full bg-slate-200">
                <div
                    class="h-full rounded-full transition-all duration-300"
                    :class="sksBarColor"
                    :style="{ width: sksPercentage + '%' }"
                />
            </div>
        </Card>

        <!-- Daftar Mata Kuliah Dipilih -->
        <Card class="mb-6">
            <template #header>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="font-semibold text-content">Mata Kuliah Dipilih</h2>
                        <p class="text-sm text-muted">Daftar mata kuliah yang sudah Anda pilih.</p>
                    </div>
                    <div v-if="canEdit && selected.length > 0" class="flex gap-2">
                        <Button variant="success" @click="confirmSubmit">
                            📨 Submit KRS
                        </Button>
                    </div>
                </div>
            </template>

            <Table
                v-if="selected.length > 0"
                :columns="selectedColumns"
                :rows="selected"
                empty-text="Belum ada mata kuliah dipilih."
            >
                <template #cell-status="{ row }">
                    <Badge :variant="statusBadgeVariant(row.status)">
                        {{ statusLabel(row.status) }}
                    </Badge>
                </template>
                <template #cell-action="{ row }">
                    <Button
                        v-if="canEdit"
                        variant="ghost"
                        size="sm"
                        @click="confirmRemove(row.id)"
                    >
                        🗑️
                    </Button>
                </template>
            </Table>
            <EmptyState v-else title="Belum ada pilihan" description="Pilih mata kuliah dari daftar di bawah." />
        </Card>

        <!-- Daftar Mata Kuliah Tersedia -->
        <Card>
            <template #header>
                <div>
                    <h2 class="font-semibold text-content">Mata Kuliah Tersedia</h2>
                    <p class="text-sm text-muted">Pilih mata kuliah yang ingin diambil.</p>
                </div>
            </template>

            <Table
                v-if="offerings.length > 0"
                :columns="availableColumns"
                :rows="offerings"
                empty-text="Tidak ada mata kuliah tersedia."
            >
                <template #cell-action="{ row }">
                    <Button
                        v-if="canEdit && !row.is_selected"
                        variant="primary"
                        size="sm"
                        @click="addCourse(row.id)"
                    >
                        ➕ Ambil
                    </Button>
                    <Badge v-else-if="row.is_selected" variant="emerald">Dipilih</Badge>
                </template>
            </Table>
            <EmptyState v-else title="Tidak ada kelas" description="Belum ada kelas yang tersedia untuk semester ini." />
        </Card>

        <!-- Confirm Remove -->
        <ConfirmDialog
            :open="removeConfirmOpen"
            title="Hapus Mata Kuliah"
            message="Apakah Anda yakin ingin menghapus mata kuliah ini dari KRS?"
            confirm-label="Hapus"
            :danger="true"
            @close="removeConfirmOpen = false"
            @confirm="removeCourse"
        />

        <!-- Confirm Submit -->
        <ConfirmDialog
            :open="submitConfirmOpen"
            title="Submit KRS"
            message="Setelah disubmit, Anda tidak dapat mengubah KRS lagi. Lanjutkan?"
            confirm-label="Submit"
            :danger="false"
            @close="submitConfirmOpen = false"
            @confirm="submitKrs"
        />
    </AuthenticatedLayout>
</template>
