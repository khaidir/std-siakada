<script setup>
import { computed } from 'vue';

const props = defineProps({
    // [{ label: 'Pengajuan Proposal', description?: '…' }, …]
    steps: { type: Array, required: true },
    // Indeks langkah yang sedang berjalan (0-based). Langkah sebelumnya dianggap selesai.
    current: { type: Number, default: 0 },
});

function stateOf(index) {
    if (index < props.current) return 'done';
    if (index === props.current) return 'current';
    return 'upcoming';
}

const lastIndex = computed(() => props.steps.length - 1);
</script>

<template>
    <ol class="flex flex-col gap-0 sm:flex-row sm:gap-2">
        <li v-for="(step, i) in steps" :key="step.label" class="flex flex-1 gap-3 sm:flex-col sm:gap-0">
            <!-- Rel indikator: vertikal di mobile, horizontal di sm ke atas -->
            <div class="flex flex-col items-center sm:w-full sm:flex-row">
                <span class="hidden h-0.5 flex-1 sm:block" :class="i === 0 ? 'bg-transparent' : stateOf(i) === 'upcoming' ? 'bg-border' : 'bg-success'"></span>

                <span
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-semibold ring-2"
                    :class="{
                        'bg-success text-success-fg ring-success': stateOf(i) === 'done',
                        'bg-primary text-primary-fg ring-primary': stateOf(i) === 'current',
                        'bg-surface text-muted ring-border': stateOf(i) === 'upcoming',
                    }"
                >
                    <svg v-if="stateOf(i) === 'done'" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span v-else>{{ i + 1 }}</span>
                </span>

                <span class="hidden h-0.5 flex-1 sm:block" :class="i === lastIndex ? 'bg-transparent' : stateOf(i + 1) === 'upcoming' ? 'bg-border' : 'bg-success'"></span>

                <!-- Rel vertikal untuk mobile -->
                <span v-if="i !== lastIndex" class="w-0.5 flex-1 sm:hidden" :class="stateOf(i + 1) === 'upcoming' ? 'bg-border' : 'bg-success'"></span>
            </div>

            <div class="pb-6 sm:pb-0 sm:pt-2 sm:text-center">
                <p
                    class="text-sm font-medium"
                    :class="stateOf(i) === 'upcoming' ? 'text-muted' : 'text-content'"
                >
                    {{ step.label }}
                </p>
                <p v-if="step.description" class="mt-0.5 text-xs text-muted">{{ step.description }}</p>

                <!-- Status ditulis eksplisit, tidak hanya lewat warna/ikon. -->
                <span class="sr-only">
                    {{ stateOf(i) === 'done' ? 'Selesai' : stateOf(i) === 'current' ? 'Sedang berjalan' : 'Belum dimulai' }}
                </span>
            </div>
        </li>
    </ol>
</template>
