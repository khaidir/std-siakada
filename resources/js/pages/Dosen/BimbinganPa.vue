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
</script>

<template>
    <AuthenticatedLayout title="Bimbingan PA">
        <template #header>
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900">Bimbingan PA — Persetujuan KRS</h1>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Flash Message -->
                <div v-if="$page.props.flash?.success" class="mb-4 rounded-lg bg-emerald-50 p-4 text-sm text-emerald-700">
                    {{ $page.props.flash.success }}
                </div>

                <!-- Table -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div v-if="plans.length === 0" class="p-6 text-center text-sm text-gray-500">
                        Belum ada KRS yang perlu disetujui.
                    </div>

                    <table v-else class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Mahasiswa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">NIM</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Program Studi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Semester</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="plan in plans" :key="plan.id" class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ plan.student?.name }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                    {{ plan.student?.nim }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                    {{ plan.student?.study_program?.name }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
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
                                        class="font-medium text-indigo-600 hover:text-indigo-900"
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
