<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import StatCard from '@/components/shared/StatCard.vue';
import DataTable from '@/components/shared/DataTable.vue';
import DonutChart from '@/components/shared/DonutChart.vue';
import RadarChart from '@/components/shared/RadarChart.vue';
import ProgressBar from '@/components/shared/ProgressBar.vue';
import Stepper from '@/components/shared/Stepper.vue';
import MiniCalendar from '@/components/shared/MiniCalendar.vue';
import UpcomingClasses from '@/components/shared/UpcomingClasses.vue';
import Card from '@/components/ui/Card.vue';
import Badge from '@/components/ui/Badge.vue';

const props = defineProps({
    stats: { type: Object, required: true },
});

/*
 * Semua seksi dibaca dari `stats` dan tetap opsional: kalau backend belum
 * mengirim sebuah bagian, seksinya tidak dirender dan halaman tetap utuh.
 */
const schedule = computed(() => props.stats.today_schedule ?? []);
const announcement = computed(() => props.stats.announcement ?? null);
const khs = computed(() => props.stats.khs ?? []);
const attendance = computed(() => props.stats.attendance ?? []);
const grades = computed(() => props.stats.grades ?? []);
const thesis = computed(() => props.stats.thesis ?? null);
const upcoming = computed(() => props.stats.upcoming ?? []);
const calendarMarks = computed(() => props.stats.calendar_marks ?? []);
// Belum ada tabel PLO/CPL di skema, jadi radar hanya tampil bila datanya benar-benar dikirim.
const plo = computed(() => props.stats.plo ?? []);

const scheduleColumns = [
    { key: 'course', label: 'Mata Kuliah' },
    { key: 'classroom', label: 'Ruangan' },
    { key: 'start_time', label: 'Mulai' },
    { key: 'end_time', label: 'Selesai' },
];

const gradeColumns = [
    { key: 'course', label: 'Mata Kuliah' },
    { key: 'sks', label: 'SKS', align: 'center' },
    { key: 'score', label: 'Nilai', align: 'center' },
    { key: 'letter_grade', label: 'Huruf', align: 'center' },
];

const THESIS_STEPS = [
    { label: 'Pengajuan Proposal' },
    { label: 'Bimbingan' },
    { label: 'Daftar Sidang' },
    { label: 'Wisuda' },
];

// Urutan status skripsi menentukan sejauh mana stepper terisi.
const THESIS_ORDER = ['proposal', 'seminar_proposal', 'sidang', 'lulus'];

const thesisStep = computed(() => {
    const index = THESIS_ORDER.indexOf(thesis.value?.status);
    return index < 0 ? 0 : index;
});

function gradeVariant(letter) {
    if (!letter) return 'neutral';
    if (letter.startsWith('A')) return 'success';
    if (letter.startsWith('B')) return 'info';
    if (letter.startsWith('C')) return 'warning';
    return 'danger';
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Dashboard Mahasiswa" />

        <PageHeader title="Dashboard Mahasiswa" subtitle="Ringkasan studi Anda semester ini." />

        <!-- Banner informasi -->
        <div
            v-if="announcement"
            class="mb-5 overflow-hidden rounded-lg bg-primary px-5 py-4 text-primary-fg shadow-sm sm:px-6 sm:py-5"
        >
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0">
                    <p class="text-base font-semibold sm:text-lg">{{ announcement.title }}</p>
                    <p v-if="announcement.body" class="mt-1 text-sm opacity-90">{{ announcement.body }}</p>
                </div>
                <Link
                    v-if="announcement.href"
                    :href="announcement.href"
                    class="shrink-0 self-start rounded-lg bg-primary-fg/15 px-4 py-2 text-sm font-medium ring-1 ring-inset ring-primary-fg/30 transition hover:bg-primary-fg/25 focus-visible:outline-none focus-visible:ring-2"
                >
                    Lihat detail
                </Link>
            </div>
        </div>

        <!-- Statistik -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <StatCard label="Total SKS" :value="stats.total_sks" icon="📚" accent="primary" />
            <StatCard label="Kelas Diikuti" :value="stats.total_classes" icon="🏫" accent="info" />
            <StatCard label="Rata-rata Nilai" :value="stats.average_grade ?? '—'" icon="📊" accent="success" />
        </div>

        <!-- KHS + Kehadiran -->
        <div v-if="khs.length || attendance.length" class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-2">
            <Card v-if="khs.length">
                <template #header>
                    <h2 class="font-semibold text-content">Komposisi Nilai (KHS)</h2>
                </template>
                <DonutChart
                    :segments="khs"
                    center-label="IPK"
                    :center-value="stats.average_grade ?? '—'"
                />
            </Card>

            <Card v-if="attendance.length">
                <template #header>
                    <h2 class="font-semibold text-content">Kehadiran per Mata Kuliah</h2>
                </template>
                <div class="space-y-4">
                    <ProgressBar
                        v-for="item in attendance"
                        :key="item.course"
                        :label="item.course"
                        :value="item.percentage"
                        accent="auto"
                        :sublabel="item.sublabel"
                    />
                </div>
            </Card>
        </div>

        <!-- Jadwal hari ini -->
        <Card class="mt-5">
            <template #header>
                <h2 class="font-semibold text-content">Jadwal Kuliah Hari Ini</h2>
            </template>
            <DataTable :columns="scheduleColumns" :rows="schedule" empty-text="Tidak ada jadwal hari ini." />
        </Card>

        <!-- Nilai akademik -->
        <Card v-if="grades.length" class="mt-5">
            <template #header>
                <h2 class="font-semibold text-content">Nilai Akademik</h2>
            </template>
            <DataTable :columns="gradeColumns" :rows="grades" empty-text="Belum ada nilai.">
                <template #cell-letter_grade="{ value }">
                    <Badge :variant="gradeVariant(value)">{{ value ?? '—' }}</Badge>
                </template>
            </DataTable>
        </Card>

        <!-- Status TA/PA -->
        <Card v-if="thesis" class="mt-5">
            <template #header>
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 class="font-semibold text-content">Status TA/PA</h2>
                    <Badge :variant="thesis.status === 'lulus' ? 'success' : 'info'" dot>
                        {{ thesis.status_label ?? thesis.status }}
                    </Badge>
                </div>
            </template>

            <p v-if="thesis.title" class="mb-5 text-sm text-muted">{{ thesis.title }}</p>
            <Stepper :steps="THESIS_STEPS" :current="thesisStep" />
        </Card>

        <!-- Radar PLO -->
        <Card v-if="plo.length" class="mt-5">
            <template #header>
                <h2 class="font-semibold text-content">Capaian Pembelajaran (PLO)</h2>
            </template>
            <RadarChart :items="plo" />
        </Card>

        <!-- Panel kanan -->
        <template #aside>
            <Card>
                <template #header>
                    <h2 class="font-semibold text-content">Kalender Akademik</h2>
                </template>
                <MiniCalendar :marked-dates="calendarMarks" />
            </Card>

            <Card>
                <template #header>
                    <h2 class="font-semibold text-content">Kelas Mendatang</h2>
                </template>
                <UpcomingClasses :classes="upcoming" />
            </Card>
        </template>
    </AuthenticatedLayout>
</template>
