<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import Card from '@/components/ui/Card.vue';
import EmptyState from '@/components/ui/EmptyState.vue';

const props = defineProps({
    schedules: Array,
    semesterLabel: String,
});

const days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];

const dayLabel = {
    senin: 'Senin',
    selasa: 'Selasa',
    rabu: 'Rabu',
    kamis: 'Kamis',
    jumat: 'Jumat',
    sabtu: 'Sabtu',
};

const groupedByDay = computed(() => {
    const groups = {};
    for (const day of days) {
        groups[day] = props.schedules.filter((s) => s.day === day);
    }
    return groups;
});

function formatTime(time) {
    if (!time) return '';
    return time.substring(0, 5);
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Jadwal Kuliah" />

        <PageHeader title="Jadwal Kuliah" :subtitle="semesterLabel ? `Jadwal semester ${semesterLabel}` : 'Jadwal perkuliahan Anda.'" />

        <div v-if="schedules.length === 0" class="py-8">
            <Card>
                <EmptyState title="Belum ada jadwal" description="Anda belum memiliki jadwal kuliah untuk semester ini." />
            </Card>
        </div>

        <div v-else class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
            <div v-for="day in days" :key="day">
                <Card>
                    <template #header>
                        <h3 class="font-semibold text-content">{{ dayLabel[day] }}</h3>
                    </template>

                    <div v-if="groupedByDay[day].length === 0" class="py-4 text-center text-sm text-muted">
                        Tidak ada kelas
                    </div>

                    <div v-for="schedule in groupedByDay[day]" :key="schedule.id" class="mb-3 rounded-lg border p-3 last:mb-0">
                        <p class="text-sm font-semibold text-content">{{ schedule.course?.code }}</p>
                        <p class="text-xs text-muted">{{ schedule.course?.name }}</p>
                        <div class="mt-1 text-xs text-muted">
                            <span>{{ formatTime(schedule.start_time) }} - {{ formatTime(schedule.end_time) }}</span>
                        </div>
                        <div class="text-xs text-muted">
                            <span>{{ schedule.lecturer?.user?.name }}</span>
                        </div>
                        <div class="text-xs text-muted">
                            <span>{{ schedule.classroom?.name }}</span>
                        </div>
                    </div>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
