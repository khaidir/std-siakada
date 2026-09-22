<script setup>
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    plans: {
        type: Array,
        default: () => [],
    },
});

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
</script>

<template>
    <AuthenticatedLayout title="Bimbingan PA">
        <template #header>
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-content">Bimbingan PA — Persetujuan KRS</h1>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Flash Message -->
                <div v-if="$page.props.flash?.success" class="mb-4 rounded-lg bg-success-subtle p-4 text-sm text-success-strong">
                    {{ $page.props.flash.success }}
                </div>

                <!-- Table -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div v-if="plans.length === 0" class="p-6 text-center text-sm text-muted">
                        Belum ada KRS yang perlu disetujui.
                    </div>

                    <table v-else class="min-w-full divide-y divide-border">
                        <thead class="bg-neutral-subtle">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted">Mahasiswa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted">NIM</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted">Program Studi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted">Semester</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <tr v-for="plan in plans" :key="plan.id" class="hover:bg-neutral-subtle">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-content">
                                    {{ plan.student?.name }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-muted">
                                    {{ plan.student?.nim }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-muted">
                                    {{ plan.student?.study_program?.name }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-muted">
                                    {{ plan.semester?.name }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium" :class="statusBadge(plan.status)">
                                        {{ statusLabel(plan.status) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <Link
                                        :href="route('dosen.bimbingan-pa.show', plan.id)"
                                        class="font-medium text-primary-strong hover:text-primary-strong"
                                    >
                                        Detail
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
