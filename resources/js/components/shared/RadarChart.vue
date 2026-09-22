<script setup>
import { computed } from 'vue';
import {
    Chart as ChartJS,
    Filler,
    LineElement,
    PointElement,
    RadialLinearScale,
    Tooltip,
} from 'chart.js';
import { Radar } from 'vue-chartjs';
import { useChartTheme } from '@/composables/useChartTheme';

ChartJS.register(RadialLinearScale, PointElement, LineElement, Filler, Tooltip);

const props = defineProps({
    // [{ label: 'PLO-1', value: 82 }, …]
    items: { type: Array, default: () => [] },
    max: { type: Number, default: 100 },
    datasetLabel: { type: String, default: 'Capaian' },
    height: { type: Number, default: 260 },
});

const { palette } = useChartTheme();

const hasData = computed(() => props.items.length >= 3);

function hexToRgba(hex, alpha) {
    const clean = String(hex).replace('#', '');
    const full = clean.length === 3 ? clean.split('').map((c) => c + c).join('') : clean;
    const num = parseInt(full, 16);
    if (Number.isNaN(num)) return `rgba(200, 35, 51, ${alpha})`;
    return `rgba(${(num >> 16) & 255}, ${(num >> 8) & 255}, ${num & 255}, ${alpha})`;
}

const chartData = computed(() => ({
    labels: props.items.map((i) => i.label),
    datasets: [
        {
            label: props.datasetLabel,
            data: props.items.map((i) => Number(i.value) || 0),
            backgroundColor: hexToRgba(palette.value.primary, 0.18),
            borderColor: palette.value.primary,
            borderWidth: 2,
            pointBackgroundColor: palette.value.primary,
            pointBorderColor: palette.value.surface,
            pointRadius: 4,
        },
    ],
}));

const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    scales: {
        r: {
            min: 0,
            max: props.max,
            angleLines: { color: palette.value.border },
            grid: { color: palette.value.border },
            pointLabels: { color: palette.value.muted, font: { size: 11 } },
            ticks: {
                color: palette.value.muted,
                backdropColor: 'transparent',
                stepSize: Math.max(1, Math.round(props.max / 4)),
            },
        },
    },
    plugins: { legend: { display: false } },
}));
</script>

<template>
    <div>
        <div :style="{ height: `${height}px` }">
            <Radar v-if="hasData" :data="chartData" :options="chartOptions" />
            <div v-else class="flex h-full items-center justify-center px-4 text-center text-sm text-muted">
                Radar membutuhkan minimal tiga indikator untuk bisa digambar.
            </div>
        </div>

        <!-- Nilai tetap terbaca screen reader tanpa harus menafsirkan grafik. -->
        <ul v-if="hasData" class="mt-3 grid grid-cols-2 gap-x-4 gap-y-1 text-xs sm:grid-cols-3">
            <li v-for="item in items" :key="item.label" class="flex items-center justify-between gap-2">
                <span class="truncate text-muted">{{ item.label }}</span>
                <span class="shrink-0 font-medium tabular-nums text-content">{{ item.value }}</span>
            </li>
        </ul>
    </div>
</template>
