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
        draft: 'bg-gray-100 text-gray-700',
        submitted: 'bg-blue-100 text-blue-700',
        approved: 'bg-emerald-100 text-emerald-700',
        rejected: 'bg-rose-100 text-rose-700',
    };
    return map[status] || 'bg-gray-100 text-gray-700';
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
        draft: 'bg-gray-100 text-gray-600',
        approved: 'bg-emerald-100 text-emerald-700',
        rejected: 'bg-rose-100 text-rose-700',
    };
    return map[status] || 'bg-gray-100 text-gray-600';
};
</script>

<template>
    <AuthenticatedLayout title="Detail KRS">
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('dosen.bimbingan-pa.index')"
                        class="text-sm text-gray-500 hover:text-gray-700"
                    >
                        &larr; Kembali
                    </Link>
                    <h1 class="text-xl font-semibold text-gray-900">Detail KRS</h1>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Info Mahasiswa -->
                <div class="mb-6 overflow-hidden rounded-lg bg-white shadow">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h2 class="text-lg font-medium text-gray-900">Informasi Mahasiswa</h2>
                    </div>
                    <div class="grid grid-cols-1 gap-4 p-6 sm:grid-cols-2">
                        <div>
                            <span class="text-sm text-gray-500">Nama</span>
                            <p class="font-medium text-gray-900">{{ plan.student?.name }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">NIM</span>
                            <p class="font-medium text-gray-900">{{ plan.student?.nim }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Program Studi</span>
                            <p class="font-medium text-gray-900">{{ plan.student?.study_program?.name }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Semester</span>
                            <p class="font-medium text-gray-900">{{ plan.semester?.name }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Status KRS</span>
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium" :class="statusBadge(plan.status)">
                                {{ statusLabel(plan.status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Daftar Mata Kuliah -->
                <div class="mb-6 overflow-hidden rounded-lg bg-white shadow">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h2 class="text-lg font-medium text-gray-900">Mata Kuliah</h2>
                    </div>
                    <div v-if="!plan.study_plan_details || plan.study_plan_details.length === 0" class="p-6 text-center text-sm text-gray-500">
                        Belum ada mata kuliah.
                    </div>
                    <table v-else class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Kode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Mata Kuliah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">SKS</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Kelas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="detail in plan.study_plan_details" :key="detail.id" class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                    {{ detail.offering?.course?.code }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ detail.offering?.course?.name }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                    {{ detail.offering?.course?.credits }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
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
                        class="rounded-lg bg-emerald-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-emerald-700 disabled:opacity-50"
                    >
                        {{ approveForm.processing ? 'Memproses...' : 'Setujui KRS' }}
                    </button>
                    <button
                        @click="showRejectModal = true"
                        class="rounded-lg border border-rose-300 bg-white px-6 py-2.5 text-sm font-medium text-rose-600 hover:bg-rose-50"
                    >
                        Tolak KRS
                    </button>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div v-if="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showRejectModal = false">
            <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                <h3 class="mb-4 text-lg font-medium text-gray-900">Tolak KRS</h3>
                <p class="mb-4 text-sm text-gray-600">Berikan alasan penolakan KRS mahasiswa ini.</p>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Alasan</label>
                    <textarea
                        v-model="rejectForm.reason"
                        rows="3"
                        class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Masukkan alasan penolakan..."
                    ></textarea>
                    <p v-if="rejectForm.errors.reason" class="mt-1 text-sm text-rose-600">{{ rejectForm.errors.reason }}</p>
                </div>

                <div class="flex justify-end gap-3">
                    <button
                        @click="showRejectModal = false"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Batal
                    </button>
                    <button
                        @click="reject"
                        :disabled="rejectForm.processing"
                        class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-medium text-white hover:bg-rose-700 disabled:opacity-50"
                    >
                        {{ rejectForm.processing ? 'Memproses...' : 'Tolak' }}
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
