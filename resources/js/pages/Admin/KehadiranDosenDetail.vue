<script setup>
import { ref } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import Card from '@/components/ui/Card.vue';
import Button from '@/components/ui/Button.vue';
import Badge from '@/components/ui/Badge.vue';
import EmptyState from '@/components/ui/EmptyState.vue';

const props = defineProps({
    lecturer_id: { type: Number, required: true },
    semester_id: { type: Number, required: true },
    detail: { type: Array, required: true },
});

const page = usePage();

function statusBadge(status) {
    const map = {
        hadir: { label: 'Hadir', variant: 'success' },
        terlambat: { label: 'Terlambat', variant: 'warning' },
        izin: { label: 'Izin', variant: 'info' },
        alpha: { label: 'Alpha', variant: 'danger' },
    };
    return map[status] ?? { label: status, variant: 'secondary' };
}

function goBack() {
    router.visit(route('admin.kehadiran.index'));
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Detail Kehadiran Dosen" />

        <PageHeader title="Detail Riwayat Kehadiran" subtitle="Riwayat kehadiran mengajar dosen.">
            <template #actions>
                <Button variant="secondary" @click="goBack">
                    ← Kembali
                </Button>
            </template>
        </PageHeader>

        <Card>
            <template #header>
                <h2 class="font-semibold text-content">Riwayat Kehadiran</h2>
            </template>

            <div v-if="detail.length === 0" class="py-8">
                <EmptyState title="Tidak ada data" description="Belum ada riwayat kehadiran untuk dosen ini pada semester tersebut." />
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-border text-xs uppercase text-muted">
                            <th class="px-4 py-3 font-medium">Tanggal</th>
                            <th class="px-4 py-3 font-medium">Kode MK</th>
                            <th class="px-4 py-3 font-medium">Mata Kuliah</th>
                            <th class="px-4 py-3 font-medium">Check In</th>
                            <th class="px-4 py-3 font-medium">Check Out</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr v-for="item in detail" :key="item.id">
                            <td class="px-4 py-3">{{ item.date }}</td>
                            <td class="px-4 py-3 font-mono text-xs">{{ item.course_code }}</td>
                            <td class="px-4 py-3">{{ item.course_name }}</td>
                            <td class="px-4 py-3">{{ item.check_in ?? '—' }}</td>
                            <td class="px-4 py-3">{{ item.check_out ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <Badge :variant="statusBadge(item.status).variant">
                                    {{ statusBadge(item.status).label }}
                                </Badge>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>
    </AuthenticatedLayout>
</template>
