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
    offerings: Array,
    selectedOfferingId: Number,
    attendances: Array,
    percentage: Number,
});

const localOfferingId = ref(props.selectedOfferingId);

const columns = [
    { key: 'meeting_number', label: 'Pertemuan' },
    { key: 'date', label: 'Tanggal' },
    { key: 'status', label: 'Status' },
];

function statusBadgeVariant(status) {
    const map = {
        hadir: 'emerald',
        izin: 'amber',
        sakit: 'blue',
        alpha: 'rose',
    };
    return map[status] ?? 'slate';
}

function statusLabel(status) {
    const map = {
        hadir: 'Hadir',
        izin: 'Izin',
        sakit: 'Sakit',
        alpha: 'Alpha',
    };
    return map[status] ?? status;
}

function formatDate(date) {
    if (!date) return '';
    const d = new Date(date);
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
}

function changeOffering() {
    router.get(route('mahasiswa.presensi.index'), { offering_id: localOfferingId.value }, { preserveState: true });
}

const percentageColor = props.percentage >= 80 ? 'text-emerald-600' : props.percentage >= 60 ? 'text-amber-600' : 'text-rose-600';
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Presensi" />

        <PageHeader title="Riwayat Presensi" subtitle="Riwayat kehadiran perkuliahan Anda." />

        <!-- Pilih Kelas -->
        <Card class="mb-6">
            <template #header>
                <h3 class="text-sm font-medium text-content">Pilih Kelas</h3>
            </template>
            <select
                v-model="localOfferingId"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:w-96"
                @change="changeOffering"
            >
                <option v-for="o in offerings" :key="o.id" :value="o.id">
                    {{ o.label }}
                </option>
            </select>
        </Card>

        <!-- Ringkasan -->
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <Card>
                <template #header>
                    <h3 class="text-sm font-medium text-muted">Persentase Kehadiran</h3>
                </template>
                <p class="text-3xl font-bold" :class="percentageColor">{{ percentage.toFixed(1) }}%</p>
            </Card>
            <Card>
                <template #header>
                    <h3 class="text-sm font-medium text-muted">Total Pertemuan</h3>
                </template>
                <p class="text-3xl font-bold text-content">{{ attendances.length }}</p>
            </Card>
            <Card>
                <template #header>
                    <h3 class="text-sm font-medium text-muted">Kehadiran</h3>
                </template>
                <p class="text-3xl font-bold text-content">{{ attendances.filter(a => a.status === 'hadir').length }}</p>
            </Card>
        </div>

        <!-- Tabel Presensi -->
        <Card>
            <template #header>
                <div>
                    <h2 class="font-semibold text-content">Riwayat Pertemuan</h2>
                    <p class="text-sm text-muted">Daftar pertemuan dan status kehadiran.</p>
                </div>
            </template>

            <div v-if="attendances.length === 0" class="py-8">
                <EmptyState title="Belum ada presensi" description="Belum ada data presensi untuk kelas ini." />
            </div>

            <Table v-else :columns="columns" :rows="attendances.map(a => ({
                meeting_number: `Pertemuan ${a.meeting_number}`,
                date: formatDate(a.date),
                status: a.status,
            }))">
                <template #cell-status="{ row }">
                    <Badge :variant="statusBadgeVariant(row.status)">
                        {{ statusLabel(row.status) }}
                    </Badge>
                </template>
            </Table>
        </Card>
    </AuthenticatedLayout>
</template>
