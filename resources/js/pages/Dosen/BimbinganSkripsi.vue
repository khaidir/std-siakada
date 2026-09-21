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
</script>

<template>
    <AuthenticatedLayout title="Bimbingan Skripsi">
        <template #header>
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-gray-900">Bimbingan Skripsi</h1>
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
                    <div v-if="theses.length === 0" class="p-6 text-center text-sm text-gray-500">
                        Belum ada mahasiswa bimbingan skripsi.
                    </div>

                    <table v-else class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Mahasiswa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">NIM</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Judul</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="thesis in theses" :key="thesis.id" class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ thesis.student?.name }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                    {{ thesis.student?.nim }}
                                </td>
                                <td class="max-w-xs truncate px-6 py-4 text-sm text-gray-600">
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
