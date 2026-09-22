<script setup>
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    plan: {
        type: Object,
        required: true,
    },
});

const showRejectModal = ref(false);

const approveForm = useForm({});
const rejectForm = useForm({
    decision: 'rejected',
    reason: '',
});

const approve = () => {
    approveForm.post(route('dosen.bimbingan-pa.approve', props.plan.id), {
        preserveScroll: true,
    });
};

const reject = () => {
    rejectForm.post(route('dosen.bimbingan-pa.reject', props.plan.id), {
        preserveScroll: true,
        onSuccess: () => {
            showRejectModal.value = false;
            rejectForm.reset();
        },
    });
};

const statusBadge = (status) => {
    const map = {
        draft: 'bg-neutral-subtle text-content',
        submitted: 'bg-info-subtle text-info-strong',
        approved: 'bg-success-subtle text-success-strong',
        rejected: 'bg-danger-subtle text-danger-strong',
    };
    return map[status] || 'bg-neutral-subtle text-content';
};

const statusLabel = (status) => {
    const map = {
        draft: 'Draft',
        submitted: 'Menunggu',
        approved: 'Disetujui',
        rejected: 'Ditolak',
    };
    return map[status] || status;
};

const detailStatusBadge = (status) => {
    const map = {
        draft: 'bg-neutral-subtle text-muted',
        approved: 'bg-success-subtle text-success-strong',
        rejected: 'bg-danger-subtle text-danger-strong',
    };
    return map[status] || 'bg-neutral-subtle text-muted';
};
</script>

<template>
    <AuthenticatedLayout title="Detail KRS">
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('dosen.bimbingan-pa.index')"
                        class="text-sm text-muted hover:text-content"
                    >
                        &larr; Kembali
                    </Link>
                    <h1 class="text-xl font-semibold text-content">Detail KRS</h1>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Info Mahasiswa -->
                <div class="mb-6 overflow-hidden rounded-lg bg-white shadow">
                    <div class="border-b border-border px-6 py-4">
                        <h2 class="text-lg font-medium text-content">Informasi Mahasiswa</h2>
                    </div>
                    <div class="grid grid-cols-1 gap-4 p-6 sm:grid-cols-2">
                        <div>
                            <span class="text-sm text-muted">Nama</span>
                            <p class="font-medium text-content">{{ plan.student?.name }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-muted">NIM</span>
                            <p class="font-medium text-content">{{ plan.student?.nim }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-muted">Program Studi</span>
                            <p class="font-medium text-content">{{ plan.student?.study_program?.name }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-muted">Semester</span>
                            <p class="font-medium text-content">{{ plan.semester?.name }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-muted">Status KRS</span>
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium" :class="statusBadge(plan.status)">
                                {{ statusLabel(plan.status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Daftar Mata Kuliah -->
                <div class="mb-6 overflow-hidden rounded-lg bg-white shadow">
                    <div class="border-b border-border px-6 py-4">
                        <h2 class="text-lg font-medium text-content">Mata Kuliah</h2>
                    </div>
                    <div v-if="!plan.study_plan_details || plan.study_plan_details.length === 0" class="p-6 text-center text-sm text-muted">
                        Belum ada mata kuliah.
                    </div>
                    <table v-else class="min-w-full divide-y divide-border">
                        <thead class="bg-neutral-subtle">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted">Kode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted">Mata Kuliah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted">SKS</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted">Kelas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <tr v-for="detail in plan.study_plan_details" :key="detail.id" class="hover:bg-neutral-subtle">
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-muted">
                                    {{ detail.offering?.course?.code }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-content">
                                    {{ detail.offering?.course?.name }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-muted">
                                    {{ detail.offering?.course?.credits }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-muted">
                                    {{ detail.offering?.class }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium" :class="detailStatusBadge(detail.status)">
                                        {{ detail.status === 'approved' ? 'Disetujui' : detail.status === 'rejected' ? 'Ditolak' : 'Draft' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Aksi -->
                <div v-if="plan.status === 'submitted'" class="flex items-center gap-4">
                    <button
                        @click="approve"
                        :disabled="approveForm.processing"
                        class="rounded-lg bg-success px-6 py-2.5 text-sm font-medium text-success-fg hover:bg-success disabled:opacity-50"
                    >
                        {{ approveForm.processing ? 'Memproses...' : 'Setujui KRS' }}
                    </button>
                    <button
                        @click="showRejectModal = true"
                        class="rounded-lg border border-danger bg-white px-6 py-2.5 text-sm font-medium text-danger-strong hover:bg-danger-subtle"
                    >
                        Tolak KRS
                    </button>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div v-if="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showRejectModal = false">
            <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                <h3 class="mb-4 text-lg font-medium text-content">Tolak KRS</h3>
                <p class="mb-4 text-sm text-muted">Berikan alasan penolakan KRS mahasiswa ini.</p>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-content">Alasan</label>
                    <textarea
                        v-model="rejectForm.reason"
                        rows="3"
                        class="mt-1 block w-full rounded-lg border border-border-strong px-3 py-2 text-sm shadow-sm focus:border-primary focus:ring-focus"
                        placeholder="Masukkan alasan penolakan..."
                    ></textarea>
                    <p v-if="rejectForm.errors.reason" class="mt-1 text-sm text-danger-strong">{{ rejectForm.errors.reason }}</p>
                </div>

                <div class="flex justify-end gap-3">
                    <button
                        @click="showRejectModal = false"
                        class="rounded-lg border border-border-strong bg-white px-4 py-2 text-sm font-medium text-content hover:bg-neutral-subtle"
                    >
                        Batal
                    </button>
                    <button
                        @click="reject"
                        :disabled="rejectForm.processing"
                        class="rounded-lg bg-danger px-4 py-2 text-sm font-medium text-danger-fg hover:bg-danger disabled:opacity-50"
                    >
                        {{ rejectForm.processing ? 'Memproses...' : 'Tolak' }}
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
