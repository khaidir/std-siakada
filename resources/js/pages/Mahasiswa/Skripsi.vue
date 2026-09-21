<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import Card from '@/components/ui/Card.vue';
import Badge from '@/components/ui/Badge.vue';
import Table from '@/components/ui/Table.vue';
import EmptyState from '@/components/ui/EmptyState.vue';

const props = defineProps({
    thesis: Object,
    logs: Array,
});

const form = ref({
    thesis_id: props.thesis?.id ?? null,
    date: '',
    activity: '',
    notes: '',
});

const submitting = ref(false);

const statusLabel = {
    proposal: 'Proposal',
    seminar_proposal: 'Seminar Proposal',
    sidang: 'Sidang',
    lulus: 'Lulus',
    revisi: 'Revisi',
};

const statusVariant = {
    proposal: 'blue',
    seminar_proposal: 'amber',
    sidang: 'violet',
    lulus: 'emerald',
    revisi: 'rose',
};

const timelineSteps = ['proposal', 'seminar_proposal', 'sidang', 'lulus'];

const columns = [
    { key: 'date', label: 'Tanggal' },
    { key: 'activity', label: 'Aktivitas' },
    { key: 'notes', label: 'Catatan' },
    { key: 'approval', label: 'Status' },
];

function formatDate(date) {
    if (!date) return '';
    const d = new Date(date);
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
}

function approvalBadgeVariant(approved) {
    return approved ? 'emerald' : 'amber';
}

function approvalLabel(approved) {
    return approved ? 'Disetujui' : 'Pending';
}

function currentStepIndex() {
    if (!props.thesis?.status) return -1;
    const idx = timelineSteps.indexOf(props.thesis.status);
    return idx;
}

function submitLog() {
    if (!form.value.thesis_id || !form.value.date || !form.value.activity) return;

    submitting.value = true;
    router.post(route('mahasiswa.skripsi.store-log'), form.value, {
        preserveScroll: true,
        onSuccess: () => {
            form.value.date = '';
            form.value.activity = '';
            form.value.notes = '';
            submitting.value = false;
        },
        onError: () => {
            submitting.value = false;
        },
    });
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Skripsi" />

        <PageHeader title="Skripsi" subtitle="Tracking bimbingan skripsi Anda." />

        <div v-if="!thesis" class="py-8">
            <Card>
                <EmptyState title="Belum ada skripsi" description="Anda belum memiliki data skripsi. Silakan hubungi admin." />
            </Card>
        </div>

        <template v-else>
            <!-- Kartu Info -->
            <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                <Card>
                    <template #header>
                        <h3 class="font-semibold text-content">Informasi Skripsi</h3>
                    </template>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-muted">Judul</dt>
                            <dd class="max-w-xs text-right font-medium text-content">{{ thesis.title }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-muted">Status</dt>
                            <dd>
                                <Badge :variant="statusVariant[thesis.status] || 'slate'">
                                    {{ statusLabel[thesis.status] || thesis.status }}
                                </Badge>
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-muted">Tanggal Pengajuan</dt>
                            <dd class="font-medium text-content">{{ formatDate(thesis.submission_date) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-muted">Pembimbing 1</dt>
                            <dd class="font-medium text-content">{{ thesis.supervisor_one?.user?.name || '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-muted">Pembimbing 2</dt>
                            <dd class="font-medium text-content">{{ thesis.supervisor_two?.user?.name || '-' }}</dd>
                        </div>
                    </dl>
                </Card>

                <!-- Timeline -->
                <Card>
                    <template #header>
                        <h3 class="font-semibold text-content">Timeline</h3>
                    </template>
                    <div class="space-y-3">
                        <div v-for="(step, idx) in timelineSteps" :key="step" class="flex items-center gap-3">
                            <div
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-bold"
                                :class="idx <= currentStepIndex() ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-400'"
                            >
                                {{ idx + 1 }}
                            </div>
                            <span
                                class="text-sm"
                                :class="idx <= currentStepIndex() ? 'font-medium text-content' : 'text-muted'"
                            >
                                {{ statusLabel[step] }}
                            </span>
                        </div>
                    </div>
                </Card>
            </div>

            <!-- Abstrak -->
            <Card class="mb-6">
                <template #header>
                    <h3 class="font-semibold text-content">Abstrak</h3>
                </template>
                <p class="text-sm text-muted">{{ thesis.abstract || 'Tidak ada abstrak.' }}</p>
            </Card>

            <!-- Log Bimbingan -->
            <Card class="mb-6">
                <template #header>
                    <div>
                        <h2 class="font-semibold text-content">Log Bimbingan</h2>
                        <p class="text-sm text-muted">Riwayat bimbingan skripsi Anda.</p>
                    </div>
                </template>

                <div v-if="logs.length === 0" class="py-4">
                    <EmptyState title="Belum ada log" description="Belum ada catatan bimbingan." />
                </div>

                <Table v-else :columns="columns" :rows="logs.map(l => ({
                    date: formatDate(l.date),
                    activity: l.activity,
                    notes: l.notes || '-',
                    approval: l.supervisor_approval,
                }))">
                    <template #cell-approval="{ row }">
                        <Badge :variant="approvalBadgeVariant(row.approval)">
                            {{ approvalLabel(row.approval) }}
                        </Badge>
                    </template>
                </Table>
            </Card>

            <!-- Form Tambah Log -->
            <Card>
                <template #header>
                    <h3 class="font-semibold text-content">Tambah Log Bimbingan</h3>
                </template>
                <form @submit.prevent="submitLog" class="space-y-4">
                    <input type="hidden" v-model="form.thesis_id" />

                    <div>
                        <label class="block text-sm font-medium text-content">Tanggal</label>
                        <input
                            v-model="form.date"
                            type="date"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:w-64"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-content">Aktivitas</label>
                        <input
                            v-model="form.activity"
                            type="text"
                            required
                            maxlength="255"
                            placeholder="Misal: Bimbingan bab 1"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-content">Catatan <span class="text-muted">(opsional)</span></label>
                        <textarea
                            v-model="form.notes"
                            maxlength="1000"
                            rows="3"
                            placeholder="Catatan bimbingan..."
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        ></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            :disabled="submitting"
                            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
                        >
                            {{ submitting ? 'Menyimpan...' : 'Simpan Log' }}
                        </button>
                    </div>
                </form>
            </Card>
        </template>
    </AuthenticatedLayout>
</template>
