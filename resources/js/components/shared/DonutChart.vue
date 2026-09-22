<script setup>
import { computed } from 'vue';
import { ArcElement, Chart as ChartJS, Legend, Tooltip } from 'chart.js';
import { Doughnut } from 'vue-chartjs';
import { useChartTheme } from '@/composables/useChartTheme';

ChartJS.register(ArcElement, Tooltip, Legend);

const props = defineProps({
    // [{ label: 'A', value: 12 }, …]
    segments: { type: Array, default: () => [] },
    centerLabel: { type: String, default: null },
    centerValue: { type: [String, Number], default: null },
    height: { type: Number, default: 200 },
});

const { palette } = useChartTheme();

const total = computed(() => props.segments.reduce((sum, s) => sum + (Number(s.value) || 0), 0));
const hasData = computed(() => total.value > 0);

const chartData = computed(() => ({
    labels: props.segments.map((s) => s.label),
    datasets: [
        {
            data: props.segments.map((s) => Number(s.value) || 0),
            backgroundColor: props.segments.map((s, i) => s.color ?? palette.value.series[i % palette.value.series.length]),
            borderColor: palette.value.surface,
            borderWidth: 2,
            hoverOffset: 6,
        },
    ],
}));

const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    cutout: '68%',
    plugins: {
        legend: { display: false },
        tooltip: {
            callbacks: {
                label: (ctx) => {
                    const value = ctx.parsed ?? 0;
                    const pct = total.value ? Math.round((value / total.value) * 100) : 0;
                    return `${ctx.label}: ${value} (${pct}%)`;
                },
            },
        },
    },
}));

function percentOf(value) {
    return total.value ? Math.round(((Number(value) || 0) / total.value) * 100) : 0;
}
</script>

<template>
    <div>
        <div class="relative" :style="{ height: `${height}px` }">
            <Doughnut v-if="hasData" :data="chartData" :options="chartOptions" />

            <div v-else class="flex h-full items-center justify-center text-sm text-muted">
                Belum ada data untuk ditampilkan.
            </div>

            <div v-if="hasData && (centerValue !== null || centerLabel)" class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
                <span v-if="centerValue !== null" class="text-2xl font-semibold tabular-nums text-content">{{ centerValue }}</span>
                <span v-if="centerLabel" class="text-xs text-muted">{{ centerLabel }}</span>
            </div>
        </div>

        <!-- Legend teks: makna tidak bergantung pada warna saja. -->
        <ul v-if="hasData" class="mt-4 space-y-1.5">
            <li v-for="(s, i) in segments" :key="s.label" class="flex items-center gap-2 text-sm">
                <span
                    class="h-2.5 w-2.5 shrink-0 rounded-full"
                    :style="{ backgroundColor: s.color ?? palette.series[i % palette.series.length] }"
                    aria-hidden="true"
                ></span>
                <span class="min-w-0 flex-1 truncate text-muted">{{ s.label }}</span>
                <span class="shrink-0 tabular-nums font-medium text-content">{{ s.value }}</span>
                <span class="w-10 shrink-0 text-right tabular-nums text-muted">{{ percentOf(s.value) }}%</span>
            </li>
        </ul>
    </div>
</template>
