<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import Card from '@/components/ui/Card.vue';
import Button from '@/components/ui/Button.vue';
import Select from '@/components/ui/Select.vue';
import Badge from '@/components/ui/Badge.vue';

const props = defineProps({
    plans: { type: Object, required: true },
    summary: { type: Object, required: true },
});

const page = usePage();

const filters = ref({
    prodi: '',
    semester: '',
    status: '',
});

const applyFilters = () => {
    router.get(route('admin.krs-monitoring.index'), filters.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilters = () => {
    filters.value = { prodi: '', semester: '', status: '' };
    router.get(route('admin.krs-monitoring.index'), {}, {
        preserveState: true,
        preserveScroll: true,
    });
};

const statusBadge = (status) => {
    const map = {
        draft: 'gray',
        submitted: 'indigo',
        approved: 'emerald',
        rejected: 'rose',
    };
    return map[status] || 'gray';
};

const statusLabel = (status) => {
    const map = {
        draft: 'Draft',
        submitted: 'Submitted',
        approved: 'Disetujui',
        rejected: 'Ditolak',
    };
    return map[status] || status;
};

const summaryCards = computed(() => [
    { label: 'Total', value: props.summary.total, color: 'text-content' },
    { label: 'Draft', value: props.summary.draft, color: 'text-muted' },
    { label: 'Submitted', value: props.summary.submitted, color: 'text-primary-strong' },
    { label: 'Disetujui', value: props.summary.approved, color: 'text-success-strong' },
    { label: 'Ditolak', value: props.summary.rejected, color: 'text-danger-strong' },
]);
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Monitoring KRS" />

        <PageHeader title="Monitoring KRS" subtitle="Pantau KRS seluruh mahasiswa." />

        <!-- Statistik Ringkas -->
        <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-5">
            <Card v-for="card in summaryCards" :key="card.label" class="text-center">
                <div class="text-2xl font-bold" :class="card.color">{{ card.value }}</div>
                <div class="text-xs text-muted">{{ card.label }}</div>
            </Card>
        </div>

        <!-- Filter -->
        <Card class="mb-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                <Select
                    v-model="filters.prodi"
                    label="Program Studi"
                    :options="[]"
                    placeholder="Semua Prodi"
                />
                <Select
                    v-model="filters.semester"
                    label="Semester"
                    :options="[]"
                    placeholder="Semua Semester"
                />
                <Select
                    v-model="filters.status"
                    label="Status"
                    :options="[
                        { value: 'draft', label: 'Draft' },
                        { value: 'submitted', label: 'Submitted' },
                        { value: 'approved', label: 'Disetujui' },
                        { value: 'rejected', label: 'Ditolak' },
                    ]"
                    placeholder="Semua Status"
                />
                <div class="flex items-end gap-2">
                    <Button variant="primary" @click="applyFilters">Terapkan</Button>
                    <Button variant="secondary" @click="resetFilters">Reset</Button>
                </div>
            </div>
        </Card>

        <!-- Tabel KRS -->
        <Card>
            <div v-if="plans.data.length === 0" class="py-6 text-center text-sm text-muted">
                Tidak ada data KRS.
            </div>

            <div v-else class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wide text-muted">
                            <th class="px-3 py-2">NIM</th>
                            <th class="px-3 py-2">Nama</th>
                            <th class="px-3 py-2">Prodi</th>
                            <th class="px-3 py-2">Semester</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2">Disetujui Oleh</th>
                            <th class="px-3 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr v-for="plan in plans.data" :key="plan.id">
                            <td class="px-3 py-2 font-mono text-content">{{ plan.student?.nim || '—' }}</td>
                            <td class="px-3 py-2 text-content">{{ plan.student?.name || '—' }}</td>
                            <td class="px-3 py-2 text-muted">{{ plan.student?.study_program?.name || '—' }}</td>
                            <td class="px-3 py-2 text-muted">{{ plan.semester?.name || '—' }}</td>
                            <td class="px-3 py-2">
                                <Badge :variant="statusBadge(plan.status)">{{ statusLabel(plan.status) }}</Badge>
                            </td>
                            <td class="px-3 py-2 text-muted">{{ plan.approved_by?.name || '—' }}</td>
                            <td class="px-3 py-2">
                                <Link
                                    :href="route('admin.krs-monitoring.show', plan.id)"
                                    class="text-sm text-primary-strong hover:underline"
                                >
                                    Detail
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="plans.last_page > 1" class="mt-4 flex items-center justify-between border-t border-border pt-4">
                <div class="text-xs text-muted">
                    Halaman {{ plans.current_page }} dari {{ plans.last_page }}
                </div>
                <div class="flex gap-2">
                    <Link
                        v-if="plans.prev_page_url"
                        :href="plans.prev_page_url"
                        class="rounded border border-border px-3 py-1 text-sm hover:bg-surface"
                    >
                        Sebelumnya
                    </Link>
                    <Link
                        v-if="plans.next_page_url"
                        :href="plans.next_page_url"
                        class="rounded border border-border px-3 py-1 text-sm hover:bg-surface"
                    >
                        Selanjutnya
                    </Link>
                </div>
            </div>
        </Card>
    </AuthenticatedLayout>
</template>
