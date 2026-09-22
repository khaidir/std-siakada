<script setup>
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    theses: {
        type: Array,
        default: () => [],
    },
});

const statusBadge = (status) => {
    const map = {
        proposal: 'bg-info-subtle text-info-strong',
        seminar_proposal: 'bg-warning-subtle text-warning-strong',
        sidang: 'bg-primary-subtle text-primary-strong',
        lulus: 'bg-success-subtle text-success-strong',
        revisi: 'bg-danger-subtle text-danger-strong',
    };
    return map[status] || 'bg-neutral-subtle text-content';
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
</script>

<template>
    <AuthenticatedLayout title="Bimbingan Skripsi">
        <template #header>
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-content">Bimbingan Skripsi</h1>
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
                    <div v-if="theses.length === 0" class="p-6 text-center text-sm text-muted">
                        Belum ada mahasiswa bimbingan skripsi.
                    </div>

                    <table v-else class="min-w-full divide-y divide-border">
                        <thead class="bg-neutral-subtle">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted">Mahasiswa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted">NIM</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted">Judul</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-muted">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <tr v-for="thesis in theses" :key="thesis.id" class="hover:bg-neutral-subtle">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-content">
                                    {{ thesis.student?.name }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-muted">
                                    {{ thesis.student?.nim }}
                                </td>
                                <td class="max-w-xs truncate px-6 py-4 text-sm text-muted">
                                    {{ thesis.title }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium" :class="statusBadge(thesis.status)">
                                        {{ statusLabel(thesis.status) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <Link
                                        :href="route('dosen.bimbingan-skripsi.show', thesis.id)"
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
