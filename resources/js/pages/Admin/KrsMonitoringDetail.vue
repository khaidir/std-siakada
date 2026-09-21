<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import Card from '@/components/ui/Card.vue';
import Badge from '@/components/ui/Badge.vue';

const props = defineProps({
    plan: { type: Object, required: true },
});

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

const totalSks = () => {
    if (!props.plan.study_plan_details) return 0;
    return props.plan.study_plan_details.reduce((sum, d) => {
        return sum + (d.course_offering?.course?.credits || 0);
    }, 0);
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Detail KRS" />

        <PageHeader title="Detail KRS" subtitle="Informasi lengkap KRS mahasiswa.">
            <template #actions>
                <Link
                    :href="route('admin.krs-monitoring.index')"
                    class="rounded border border-border px-3 py-1 text-sm hover:bg-surface"
                >
                    ← Kembali
                </Link>
            </template>
        </PageHeader>

        <!-- Info Mahasiswa -->
        <Card class="mb-6">
            <template #header>
                <h2 class="font-semibold text-content">Informasi Mahasiswa</h2>
            </template>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <div class="text-xs text-muted">NIM</div>
                    <div class="font-mono text-content">{{ plan.student?.nim || '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-muted">Nama</div>
                    <div class="text-content">{{ plan.student?.name || '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-muted">Program Studi</div>
                    <div class="text-content">{{ plan.student?.study_program?.name || '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-muted">Semester</div>
                    <div class="text-content">{{ plan.semester?.name || '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-muted">Status</div>
                    <Badge :variant="statusBadge(plan.status)">{{ statusLabel(plan.status) }}</Badge>
                </div>
                <div>
                    <div class="text-xs text-muted">Total SKS</div>
                    <div class="text-content font-semibold">{{ totalSks() }}</div>
                </div>
            </div>
        </Card>

        <!-- Riwayat Approval -->
        <Card class="mb-6">
            <template #header>
                <h2 class="font-semibold text-content">Riwayat Approval</h2>
            </template>

            <div v-if="plan.approved_by" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <div class="text-xs text-muted">Disetujui Oleh</div>
                    <div class="text-content">{{ plan.approved_by?.name || '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-muted">Tanggal Approval</div>
                    <div class="text-content">{{ plan.approved_at ? new Date(plan.approved_at).toLocaleDateString('id-ID') : '—' }}</div>
                </div>
            </div>
            <div v-else class="text-sm text-muted">Belum ada approval.</div>
        </Card>

        <!-- Daftar Mata Kuliah -->
        <Card>
            <template #header>
                <h2 class="font-semibold text-content">Mata Kuliah ({{ plan.study_plan_details?.length || 0 }})</h2>
            </template>

            <div v-if="!plan.study_plan_details || plan.study_plan_details.length === 0" class="py-6 text-center text-sm text-muted">
                Belum ada mata kuliah.
            </div>

            <div v-else class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wide text-muted">
                            <th class="px-3 py-2">Kode</th>
                            <th class="px-3 py-2">Mata Kuliah</th>
                            <th class="px-3 py-2">Kelas</th>
                            <th class="px-3 py-2">SKS</th>
                            <th class="px-3 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr v-for="detail in plan.study_plan_details" :key="detail.id">
                            <td class="px-3 py-2 font-mono text-content">{{ detail.course_offering?.course?.code || '—' }}</td>
                            <td class="px-3 py-2 text-content">{{ detail.course_offering?.course?.name || '—' }}</td>
                            <td class="px-3 py-2 text-muted">{{ detail.course_offering?.class || '—' }}</td>
                            <td class="px-3 py-2 text-content">{{ detail.course_offering?.course?.credits || 0 }}</td>
                            <td class="px-3 py-2">
                                <Badge :variant="statusBadge(detail.status)">{{ statusLabel(detail.status) }}</Badge>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>
    </AuthenticatedLayout>
</template>
