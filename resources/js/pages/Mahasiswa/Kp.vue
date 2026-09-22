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
    internship: Object,
    logs: Array,
});

const form = ref({
    internship_id: props.internship?.id ?? null,
    date: '',
    activity: '',
    notes: '',
});

const submitting = ref(false);

const statusLabel = {
    draft: 'Draft',
    berjalan: 'Berjalan',
    selesai: 'Selesai',
    ditolak: 'Ditolak',
};

const statusVariant = {
    draft: 'slate',
    berjalan: 'blue',
    selesai: 'emerald',
    ditolak: 'rose',
};

const approvalLabel = {
    pending: 'Pending',
    approved: 'Disetujui',
    rejected: 'Ditolak',
};

const approvalVariant = {
    pending: 'amber',
    approved: 'emerald',
    rejected: 'rose',
};

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

function submitLog() {
    if (!form.value.internship_id || !form.value.date || !form.value.activity) return;

    submitting.value = true;
    router.post(route('mahasiswa.kp.store-log'), form.value, {
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
        <Head title="Kerja Praktek" />

        <PageHeader title="Kerja Praktek (KP)" subtitle="Tracking kerja praktek Anda." />

        <div v-if="!internship" class="py-8">
            <Card>
                <EmptyState title="Belum ada KP" description="Anda belum memiliki data kerja praktek. Silakan hubungi admin." />
            </Card>
        </div>

        <template v-else>
            <!-- Kartu Info -->
            <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                <Card>
                    <template #header>
                        <h3 class="font-semibold text-content">Informasi KP</h3>
                    </template>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-muted">Perusahaan</dt>
                            <dd class="max-w-xs text-right font-medium text-content">{{ internship.company_name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-muted">Alamat</dt>
                            <dd class="max-w-xs text-right font-medium text-content">{{ internship.address || '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-muted">Status</dt>
                            <dd>
                                <Badge :variant="statusVariant[internship.status] || 'slate'">
                                    {{ statusLabel[internship.status] || internship.status }}
                                </Badge>
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-muted">Dosen Pembimbing</dt>
                            <dd class="font-medium text-content">{{ internship.supervisor?.user?.name || '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-muted">Pembimbing Lapangan</dt>
                            <dd class="font-medium text-content">{{ internship.field_supervisor || '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-muted">Periode</dt>
                            <dd class="font-medium text-content">
                                {{ formatDate(internship.start_date) }} - {{ formatDate(internship.end_date) }}
                            </dd>
                        </div>
                    </dl>
                </Card>
            </div>

            <!-- Logbook -->
            <Card class="mb-6">
                <template #header>
                    <div>
                        <h2 class="font-semibold text-content">Logbook Harian</h2>
                        <p class="text-sm text-muted">Catatan kegiatan harian KP Anda.</p>
                    </div>
                </template>

                <div v-if="logs.length === 0" class="py-4">
                    <EmptyState title="Belum ada logbook" description="Belum ada catatan kegiatan harian." />
                </div>

                <Table v-else :columns="columns" :rows="logs.map(l => ({
                    date: formatDate(l.date),
                    activity: l.activity,
                    notes: l.notes || '-',
                    approval: l.approval,
                }))">
                    <template #cell-approval="{ row }">
                        <Badge :variant="approvalVariant[row.approval] || 'slate'">
                            {{ approvalLabel[row.approval] || row.approval }}
                        </Badge>
                    </template>
                </Table>
            </Card>

            <!-- Form Tambah Logbook -->
            <Card>
                <template #header>
                    <h3 class="font-semibold text-content">Tambah Logbook</h3>
                </template>
                <form @submit.prevent="submitLog" class="space-y-4">
                    <input type="hidden" v-model="form.internship_id" />

                    <div>
                        <label class="block text-sm font-medium text-content">Tanggal</label>
                        <input
                            v-model="form.date"
                            type="date"
                            required
                            class="mt-1 block w-full rounded-md border-border-strong shadow-sm focus:border-primary focus:ring-focus sm:w-64"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-content">Aktivitas</label>
                        <input
                            v-model="form.activity"
                            type="text"
                            required
                            maxlength="255"
                            placeholder="Misal: Mempelajari arsitektur sistem"
                            class="mt-1 block w-full rounded-md border-border-strong shadow-sm focus:border-primary focus:ring-focus"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-content">Catatan <span class="text-muted">(opsional)</span></label>
                        <textarea
                            v-model="form.notes"
                            maxlength="1000"
                            rows="3"
                            placeholder="Catatan kegiatan..."
                            class="mt-1 block w-full rounded-md border-border-strong shadow-sm focus:border-primary focus:ring-focus"
                        ></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            :disabled="submitting"
                            class="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-fg hover:bg-primary disabled:opacity-50"
                        >
                            {{ submitting ? 'Menyimpan...' : 'Simpan Logbook' }}
                        </button>
                    </div>
                </form>
            </Card>
        </template>
    </AuthenticatedLayout>
</template>
