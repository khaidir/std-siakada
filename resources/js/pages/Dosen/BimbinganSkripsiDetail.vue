<script setup>
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    thesis: {
        type: Object,
        required: true,
    },
});

const showStatusModal = ref(false);

const statusForm = useForm({
    status: props.thesis?.status || 'proposal',
});

const logForm = useForm({
    log_id: 0,
});

const statusOptions = [
    { value: 'proposal', label: 'Proposal' },
    { value: 'seminar_proposal', label: 'Seminar Proposal' },
    { value: 'sidang', label: 'Sidang' },
    { value: 'lulus', label: 'Lulus' },
    { value: 'revisi', label: 'Revisi' },
];

const updateStatus = () => {
    statusForm.put(route('dosen.bimbingan-skripsi.update-status', props.thesis.id), {
        preserveScroll: true,
        onSuccess: () => {
            showStatusModal.value = false;
        },
    });
};

const approveLog = (logId) => {
    logForm.log_id = logId;
    logForm.post(route('dosen.bimbingan-skripsi.approve-log', props.thesis.id), {
        preserveScroll: true,
    });
};

const statusBadge = (status) => {
    const map = {
        proposal: 'bg-blue-100 text-blue-700',
        seminar_proposal: 'bg-amber-100 text-amber-700',
        sidang: 'bg-purple-100 text-purple-700',
        lulus: 'bg-emerald-100 text-emerald-700',
        revisi: 'bg-rose-100 text-rose-700',
    };
    return map[status] || 'bg-gray-100 text-gray-700';
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

const approvalBadge = (approved) => {
    return approved
        ? 'bg-emerald-100 text-emerald-700'
        : 'bg-amber-100 text-amber-700';
};

const approvalLabel = (approved) => {
    return approved ? 'Disetujui' : 'Menunggu';
};
</script>

<template>
    <AuthenticatedLayout title="Detail Skripsi">
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('dosen.bimbingan-skripsi.index')"
                        class="text-sm text-gray-500 hover:text-gray-700"
                    >
                        &larr; Kembali
                    </Link>
                    <h1 class="text-xl font-semibold text-gray-900">Detail Skripsi</h1>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Flash Message -->
                <div v-if="$page.props.flash?.success" class="mb-4 rounded-lg bg-emerald-50 p-4 text-sm text-emerald-700">
                    {{ $page.props.flash.success }}
                </div>

                <!-- Info Skripsi -->
                <div class="mb-6 overflow-hidden rounded-lg bg-white shadow">
                    <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                        <h2 class="text-lg font-medium text-gray-900">Informasi Skripsi</h2>
                        <button
                            @click="showStatusModal = true"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                        >
                            Ubah Status
                        </button>
                    </div>
                    <div class="grid grid-cols-1 gap-4 p-6 sm:grid-cols-2">
                        <div>
                            <span class="text-sm text-gray-500">Mahasiswa</span>
                            <p class="font-medium text-gray-900">{{ thesis.student?.name }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">NIM</span>
                            <p class="font-medium text-gray-900">{{ thesis.student?.nim }}</p>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="text-sm text-gray-500">Judul</span>
                            <p class="font-medium text-gray-900">{{ thesis.title }}</p>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="text-sm text-gray-500">Abstrak</span>
                            <p class="text-sm text-gray-700">{{ thesis.abstract || '-' }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Pembimbing 1</span>
                            <p class="font-medium text-gray-900">{{ thesis.supervisor_one?.user?.name || '-' }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Pembimbing 2</span>
                            <p class="font-medium text-gray-900">{{ thesis.supervisor_two?.user?.name || '-' }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Status</span>
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium" :class="statusBadge(thesis.status)">
                                {{ statusLabel(thesis.status) }}
                            </span>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Tanggal Pengajuan</span>
                            <p class="font-medium text-gray-900">{{ thesis.submission_date || '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Log Bimbingan -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h2 class="text-lg font-medium text-gray-900">Log Bimbingan</h2>
                    </div>

                    <div v-if="!thesis.thesis_logs || thesis.thesis_logs.length === 0" class="p-6 text-center text-sm text-gray-500">
                        Belum ada log bimbingan.
                    </div>

                    <div v-else class="divide-y divide-gray-200">
                        <div v-for="log in thesis.thesis_logs" :key="log.id" class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <span class="text-sm text-gray-500">{{ log.date }}</span>
                                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium" :class="approvalBadge(log.supervisor_approval)">
                                            {{ approvalLabel(log.supervisor_approval) }}
                                        </span>
                                    </div>
                                    <p class="mt-2 font-medium text-gray-900">{{ log.activity }}</p>
                                    <p v-if="log.notes" class="mt-1 text-sm text-gray-600">{{ log.notes }}</p>
                                </div>
                                <div v-if="!log.supervisor_approval" class="ml-4 flex-shrink-0">
                                    <button
                                        @click="approveLog(log.id)"
                                        :disabled="logForm.processing && logForm.log_id === log.id"
                                        class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-emerald-700 disabled:opacity-50"
                                    >
                                        {{ logForm.processing && logForm.log_id === log.id ? '...' : 'Setujui' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Modal -->
        <div v-if="showStatusModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showStatusModal = false">
            <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                <h3 class="mb-4 text-lg font-medium text-gray-900">Ubah Status Skripsi</h3>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select
                        v-model="statusForm.status"
                        class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">
                            {{ opt.label }}
                        </option>
                    </select>
                    <p v-if="statusForm.errors.status" class="mt-1 text-sm text-rose-600">{{ statusForm.errors.status }}</p>
                </div>

                <div class="flex justify-end gap-3">
                    <button
                        @click="showStatusModal = false"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Batal
                    </button>
                    <button
                        @click="updateStatus"
                        :disabled="statusForm.processing"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
                    >
                        {{ statusForm.processing ? 'Memproses...' : 'Simpan' }}
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
