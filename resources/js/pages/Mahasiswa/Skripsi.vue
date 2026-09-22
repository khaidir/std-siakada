<script setup>
import { computed, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import Stepper from '@/components/shared/Stepper.vue';
import DataTable from '@/components/shared/DataTable.vue';
import Card from '@/components/ui/Card.vue';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import Input from '@/components/ui/Input.vue';
import Textarea from '@/components/ui/Textarea.vue';
import Tabs from '@/components/ui/Tabs.vue';
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
const errors = ref({});
const activeTab = ref('status');

const tabs = [
    { value: 'status', label: 'Status TA/PA' },
    { value: 'bimbingan', label: 'Bimbingan Online' },
];

const statusLabel = {
    proposal: 'Proposal',
    seminar_proposal: 'Seminar Proposal',
    sidang: 'Sidang',
    lulus: 'Lulus',
    revisi: 'Revisi',
};

const statusVariant = {
    proposal: 'info',
    seminar_proposal: 'warning',
    sidang: 'primary',
    lulus: 'success',
    revisi: 'danger',
};

// Tahapan resmi TA/PA sesuai spesifikasi desain.
const steps = [
    { label: 'Pengajuan Proposal' },
    { label: 'Bimbingan' },
    { label: 'Daftar Sidang' },
    { label: 'Wisuda' },
];

const timelineSteps = ['proposal', 'seminar_proposal', 'sidang', 'lulus'];

const currentStep = computed(() => {
    const index = timelineSteps.indexOf(props.thesis?.status);
    // Status 'revisi' tidak memundurkan tahapan; mahasiswa tetap di fase bimbingan.
    if (index < 0) return props.thesis?.status === 'revisi' ? 1 : 0;
    return index;
});

const columns = [
    { key: 'date', label: 'Tanggal' },
    { key: 'activity', label: 'Aktivitas' },
    { key: 'notes', label: 'Catatan' },
    { key: 'approval', label: 'Status', align: 'center' },
];

const rows = computed(() =>
    (props.logs ?? []).map((log) => ({
        date: formatDate(log.date),
        activity: log.activity,
        notes: log.notes || '—',
        approval: log.supervisor_approval,
    })),
);

function formatDate(date) {
    if (!date) return '';
    return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
}

function submitLog() {
    if (!form.value.thesis_id) return;

    submitting.value = true;
    errors.value = {};

    router.post(route('mahasiswa.skripsi.store-log'), form.value, {
        preserveScroll: true,
        onSuccess: () => {
            form.value.date = '';
            form.value.activity = '';
            form.value.notes = '';
        },
        onError: (bag) => (errors.value = bag),
        onFinish: () => (submitting.value = false),
    });
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="TA/PA" />

        <PageHeader
            title="TA/PA"
            subtitle="Status tugas akhir dan riwayat bimbingan Anda."
            :breadcrumbs="['TA/PA', 'Bimbingan Online']"
        />

        <Card v-if="!thesis">
            <EmptyState
                title="Belum ada data TA/PA"
                description="Anda belum terdaftar pada tugas akhir. Silakan hubungi admin program studi."
            />
        </Card>

        <template v-else>
            <!-- Identitas TA/PA -->
            <Card class="mb-5" accent="primary">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-medium uppercase tracking-wide text-muted">Judul Tugas Akhir</p>
                        <h2 class="mt-1 text-lg font-semibold text-content">{{ thesis.title }}</h2>
                        <p v-if="thesis.submission_date" class="mt-1 text-sm text-muted">
                            Diajukan {{ formatDate(thesis.submission_date) }}
                        </p>
                    </div>
                    <Badge :variant="statusVariant[thesis.status] ?? 'neutral'" dot>
                        {{ statusLabel[thesis.status] ?? thesis.status }}
                    </Badge>
                </div>

                <dl class="mt-5 grid grid-cols-1 gap-4 border-t border-border pt-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs text-muted">Pembimbing 1</dt>
                        <dd class="mt-0.5 text-sm font-medium text-content">
                            {{ thesis.supervisor_one?.user?.name || '—' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-muted">Pembimbing 2</dt>
                        <dd class="mt-0.5 text-sm font-medium text-content">
                            {{ thesis.supervisor_two?.user?.name || '—' }}
                        </dd>
                    </div>
                </dl>
            </Card>

            <Tabs v-model="activeTab" :tabs="tabs" class="mb-5" />

            <!-- Tab: Status -->
            <template v-if="activeTab === 'status'">
                <Card class="mb-5">
                    <template #header>
                        <h3 class="font-semibold text-content">Tahapan</h3>
                    </template>
                    <Stepper :steps="steps" :current="currentStep" />
                </Card>

                <Card>
                    <template #header>
                        <h3 class="font-semibold text-content">Abstrak</h3>
                    </template>
                    <p class="text-sm leading-relaxed text-muted">{{ thesis.abstract || 'Belum ada abstrak.' }}</p>
                </Card>
            </template>

            <!-- Tab: Bimbingan Online -->
            <template v-else>
                <Card class="mb-5">
                    <template #header>
                        <div>
                            <h3 class="font-semibold text-content">Riwayat Bimbingan</h3>
                            <p class="mt-0.5 text-sm text-muted">Catatan bimbingan yang sudah Anda ajukan.</p>
                        </div>
                    </template>

                    <DataTable :columns="columns" :rows="rows" empty-text="Belum ada catatan bimbingan.">
                        <template #cell-approval="{ row }">
                            <Badge :variant="row.approval ? 'success' : 'warning'" dot>
                                {{ row.approval ? 'Disetujui' : 'Menunggu' }}
                            </Badge>
                        </template>
                    </DataTable>
                </Card>

                <Card>
                    <template #header>
                        <h3 class="font-semibold text-content">Tambah Catatan Bimbingan</h3>
                    </template>

                    <form class="space-y-4" @submit.prevent="submitLog">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <Input
                                v-model="form.date"
                                label="Tanggal"
                                type="date"
                                required
                                :error="errors.date"
                            />
                            <Input
                                v-model="form.activity"
                                label="Aktivitas"
                                placeholder="Misal: Bimbingan bab 1"
                                required
                                :error="errors.activity"
                            />
                        </div>

                        <Textarea
                            v-model="form.notes"
                            label="Catatan"
                            hint="Opsional"
                            :rows="3"
                            :maxlength="1000"
                            placeholder="Catatan bimbingan…"
                            :error="errors.notes"
                        />

                        <div class="flex justify-end">
                            <Button type="submit" :loading="submitting" class="w-full sm:w-auto">
                                {{ submitting ? 'Menyimpan…' : 'Simpan Catatan' }}
                            </Button>
                        </div>
                    </form>
                </Card>
            </template>
        </template>
    </AuthenticatedLayout>
</template>
