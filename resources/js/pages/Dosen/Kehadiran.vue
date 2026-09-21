<script setup>
import { ref } from 'vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import Card from '@/components/ui/Card.vue';
import Button from '@/components/ui/Button.vue';
import Select from '@/components/ui/Select.vue';
import Input from '@/components/ui/Input.vue';
import Badge from '@/components/ui/Badge.vue';

const props = defineProps({
    offerings: { type: Array, required: true },
    attendances: { type: Array, required: true },
});

const page = usePage();

const selectedOffering = ref(props.offerings[0]?.id ?? '');
const date = ref(new Date().toISOString().slice(0, 10));

const offeringOptions = computed(() =>
    props.offerings.map((o) => ({
        value: o.id,
        label: `${o.course?.code} - ${o.course?.name} (${o.day})`,
    })),
);

import { computed } from 'vue';

const checkInForm = useForm({
    course_offering_id: null,
    date: null,
    check_in: null,
});

const doCheckIn = () => {
    checkInForm.course_offering_id = Number(selectedOffering.value);
    checkInForm.date = date.value;
    checkInForm.check_in = new Date().toTimeString().slice(0, 8);
    checkInForm.post(route('dosen.kehadiran.check-in'), { preserveScroll: true });
};

const doCheckOut = (attendanceId) => {
    router.post(
        route('dosen.kehadiran.check-out', attendanceId),
        {},
        { preserveScroll: true },
    );
};

const statusBadge = (status) => {
    const map = {
        hadir: 'emerald',
        terlambat: 'amber',
        izin: 'indigo',
        alpha: 'rose',
    };
    return map[status] || 'gray';
};

const statusLabel = (status) => {
    const map = {
        hadir: 'Hadir',
        terlambat: 'Terlambat',
        izin: 'Izin',
        alpha: 'Alpha',
    };
    return map[status] || status;
};

const formatTime = (time) => {
    if (!time) return '—';
    return time.slice(0, 5);
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Kehadiran Mengajar" />

        <PageHeader title="Kehadiran Mengajar" subtitle="Check-in / Check-out kehadiran mengajar per pertemuan." />

        <div v-if="page.props.flash?.success" class="mb-4 rounded-md bg-emerald-50 p-3 text-sm text-emerald-700">
            {{ page.props.flash.success }}
        </div>

        <div v-if="checkInForm.hasErrors" class="mb-4 rounded-md bg-rose-50 p-3 text-sm text-rose-700">
            <p v-if="checkInForm.errors.course_offering_id">{{ checkInForm.errors.course_offering_id }}</p>
            <p v-else-if="checkInForm.errors.date">{{ checkInForm.errors.date }}</p>
            <p v-else>Periksa kembali input.</p>
        </div>

        <!-- Check-in Form -->
        <Card class="mb-6">
            <template #header>
                <h2 class="font-semibold text-content">Check-in / Check-out</h2>
            </template>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <Select
                    v-model="selectedOffering"
                    label="Kelas"
                    :options="offeringOptions"
                    placeholder="Pilih kelas…"
                />
                <Input v-model="date" type="date" label="Tanggal" />
                <div class="flex items-end gap-3">
                    <Button variant="primary" :disabled="!selectedOffering || checkInForm.processing" @click="doCheckIn">
                        ✅ Check-in
                    </Button>
                </div>
            </div>
        </Card>

        <!-- Riwayat Kehadiran -->
        <Card>
            <template #header>
                <h2 class="font-semibold text-content">Riwayat Kehadiran Mengajar</h2>
            </template>

            <div v-if="attendances.length === 0" class="py-6 text-center text-sm text-muted">
                Belum ada riwayat kehadiran mengajar.
            </div>

            <div v-else class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wide text-muted">
                            <th class="px-3 py-2">Tanggal</th>
                            <th class="px-3 py-2">Kelas</th>
                            <th class="px-3 py-2">Mata Kuliah</th>
                            <th class="px-3 py-2">Check-in</th>
                            <th class="px-3 py-2">Check-out</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr v-for="att in attendances" :key="att.id">
                            <td class="px-3 py-2 text-content">{{ att.date }}</td>
                            <td class="px-3 py-2 text-muted">{{ att.course_offering?.class || '—' }}</td>
                            <td class="px-3 py-2 text-content">{{ att.course_offering?.course?.name || '—' }}</td>
                            <td class="px-3 py-2 font-mono text-content">{{ formatTime(att.check_in) }}</td>
                            <td class="px-3 py-2 font-mono text-content">{{ formatTime(att.check_out) }}</td>
                            <td class="px-3 py-2">
                                <Badge :variant="statusBadge(att.status)">{{ statusLabel(att.status) }}</Badge>
                            </td>
                            <td class="px-3 py-2">
                                <Button
                                    v-if="!att.check_out"
                                    variant="secondary"
                                    size="sm"
                                    @click="doCheckOut(att.id)"
                                >
                                    Check-out
                                </Button>
                                <span v-else class="text-xs text-muted">Selesai</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>
    </AuthenticatedLayout>
</template>
