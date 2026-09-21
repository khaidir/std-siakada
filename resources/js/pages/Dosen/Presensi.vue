<script setup>
import { computed, ref, watch } from 'vue';
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
    students: { type: Array, required: true },
    recap: { type: Array, required: true },
    selected_offering_id: { type: [Number, String], default: null },
});

const page = usePage();

const selectedOffering = ref(props.selected_offering_id ?? (props.offerings[0]?.id ?? ''));
const meetingNumber = ref(1);
const date = ref(new Date().toISOString().slice(0, 10));

const statusOptions = [
    { value: 'hadir', label: 'Hadir' },
    { value: 'izin', label: 'Izin' },
    { value: 'sakit', label: 'Sakit' },
    { value: 'alpha', label: 'Alpha' },
];

const statusBadgeVariant = {
    hadir: 'emerald',
    izin: 'amber',
    sakit: 'indigo',
    alpha: 'rose',
};

const offeringOptions = computed(() =>
    props.offerings.map((o) => ({ value: o.id, label: o.label })),
);

const rows = ref(
    props.students.map((s) => ({
        student_id: s.student_id,
        nim: s.nim,
        name: s.name,
        status: s.status ?? 'hadir',
    })),
);

watch(
    () => props.students,
    (students) => {
        rows.value = students.map((s) => ({
            student_id: s.student_id,
            nim: s.nim,
            name: s.name,
            status: s.status ?? 'hadir',
        }));
    },
);

watch(selectedOffering, (value) => {
    if (value === '' || value === null) return;
    router.get(
        route('dosen.presensi.index'),
        { offering: value },
        { preserveState: true, preserveScroll: true, replace: true },
    );
});

const form = useForm({
    course_offering_id: null,
    meeting_number: null,
    date: null,
    attendances: [],
});

function formatDate(value) {
    if (!value) return '—';
    const [y, m, d] = value.split('-');
    return `${d}/${m}/${y}`;
}

function save() {
    form.course_offering_id = Number(selectedOffering.value);
    form.meeting_number = Number(meetingNumber.value);
    form.date = date.value;
    form.attendances = rows.value.map((r) => ({
        student_id: r.student_id,
        status: r.status,
    }));
    form.post(route('dosen.presensi.store'), { preserveScroll: true });
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Presensi Kelas" />

        <PageHeader title="Presensi Kelas" subtitle="Catat kehadiran mahasiswa per pertemuan." />

        <div v-if="page.props.flash?.success" class="mb-4 rounded-md bg-emerald-50 p-3 text-sm text-emerald-700">
            {{ page.props.flash.success }}
        </div>

        <div v-if="form.hasErrors" class="mb-4 rounded-md bg-rose-50 p-3 text-sm text-rose-700">
            <p v-if="form.errors.attendances">{{ form.errors.attendances }}</p>
            <p v-else>Periksa kembali input presensi.</p>
        </div>

        <Card class="mb-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <Select
                    v-model="selectedOffering"
                    label="Kelas"
                    :options="offeringOptions"
                    placeholder="Pilih kelas…"
                />
                <Input v-model="meetingNumber" type="number" label="Nomor Pertemuan" min="1" />
                <Input v-model="date" type="date" label="Tanggal" />
            </div>
        </Card>

        <Card class="mb-6">
            <template #header>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="font-semibold text-content">Daftar Mahasiswa</h2>
                        <p class="text-sm text-muted">Tentukan status kehadiran setiap mahasiswa.</p>
                    </div>
                    <Button variant="primary" :disabled="rows.length === 0 || form.processing" @click="save">
                        💾 Simpan Presensi
                    </Button>
                </div>
            </template>

            <div v-if="rows.length === 0" class="py-8 text-center text-sm text-muted">
                Belum ada mahasiswa terdaftar pada kelas ini.
            </div>

            <div v-else class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wide text-muted">
                            <th class="px-3 py-2">NIM</th>
                            <th class="px-3 py-2">Nama</th>
                            <th class="px-3 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr v-for="row in rows" :key="row.student_id">
                            <td class="px-3 py-2 font-mono text-muted">{{ row.nim }}</td>
                            <td class="px-3 py-2 font-medium text-content">{{ row.name }}</td>
                            <td class="px-3 py-2">
                                <div class="flex flex-wrap gap-3">
                                    <label
                                        v-for="opt in statusOptions"
                                        :key="opt.value"
                                        class="flex cursor-pointer items-center gap-1.5 text-sm"
                                    >
                                        <input
                                            v-model="row.status"
                                            type="radio"
                                            :value="opt.value"
                                            class="text-indigo-600 focus:ring-indigo-500"
                                        />
                                        <span class="text-content">{{ opt.label }}</span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>

        <Card>
            <template #header>
                <h2 class="font-semibold text-content">Rekap Pertemuan Sebelumnya</h2>
            </template>

            <div v-if="recap.length === 0" class="py-6 text-center text-sm text-muted">
                Belum ada rekap presensi untuk kelas ini.
            </div>

            <div v-else class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wide text-muted">
                            <th class="px-3 py-2">Pertemuan</th>
                            <th class="px-3 py-2">Tanggal</th>
                            <th class="px-3 py-2">Hadir</th>
                            <th class="px-3 py-2">Izin</th>
                            <th class="px-3 py-2">Sakit</th>
                            <th class="px-3 py-2">Alpha</th>
                            <th class="px-3 py-2">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr v-for="item in recap" :key="`${item.meeting_number}-${item.date}`">
                            <td class="px-3 py-2 font-medium text-content">Pertemuan {{ item.meeting_number }}</td>
                            <td class="px-3 py-2 text-muted">{{ formatDate(item.date) }}</td>
                            <td class="px-3 py-2">
                                <Badge variant="emerald">{{ item.hadir }}</Badge>
                            </td>
                            <td class="px-3 py-2">
                                <Badge variant="amber">{{ item.izin }}</Badge>
                            </td>
                            <td class="px-3 py-2">
                                <Badge variant="indigo">{{ item.sakit }}</Badge>
                            </td>
                            <td class="px-3 py-2">
                                <Badge variant="rose">{{ item.alpha }}</Badge>
                            </td>
                            <td class="px-3 py-2 font-medium text-content">{{ item.total }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>
    </AuthenticatedLayout>
</template>
