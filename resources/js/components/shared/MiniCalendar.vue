<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    // Tanggal yang diberi penanda, format 'YYYY-MM-DD'.
    markedDates: { type: Array, default: () => [] },
    // Untuk pengujian / tampilan bulan tertentu; default bulan berjalan.
    initialDate: { type: String, default: null },
});

const NAMA_BULAN = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
];
const NAMA_HARI = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];

const today = props.initialDate ? new Date(props.initialDate) : new Date();
const cursor = ref(new Date(today.getFullYear(), today.getMonth(), 1));

const todayKey = toKey(today);
const marked = computed(() => new Set(props.markedDates));

function toKey(date) {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
}

const title = computed(() => `${NAMA_BULAN[cursor.value.getMonth()]} ${cursor.value.getFullYear()}`);

const cells = computed(() => {
    const year = cursor.value.getFullYear();
    const month = cursor.value.getMonth();

    const firstOfMonth = new Date(year, month, 1);
    // getDay(): 0 = Minggu. Digeser agar pekan dimulai Senin.
    const offset = (firstOfMonth.getDay() + 6) % 7;
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    const result = [];
    for (let i = 0; i < offset; i += 1) result.push(null);
    for (let day = 1; day <= daysInMonth; day += 1) {
        const date = new Date(year, month, day);
        result.push({ day, key: toKey(date) });
    }
    return result;
});

function shift(step) {
    cursor.value = new Date(cursor.value.getFullYear(), cursor.value.getMonth() + step, 1);
}
</script>

<template>
    <div>
        <div class="mb-3 flex items-center justify-between gap-2">
            <button
                type="button"
                class="flex h-8 w-8 items-center justify-center rounded-lg text-muted transition hover:bg-surface-muted hover:text-content focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus"
                aria-label="Bulan sebelumnya"
                @click="shift(-1)"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <p class="text-sm font-semibold text-content" aria-live="polite">{{ title }}</p>

            <button
                type="button"
                class="flex h-8 w-8 items-center justify-center rounded-lg text-muted transition hover:bg-surface-muted hover:text-content focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus"
                aria-label="Bulan berikutnya"
                @click="shift(1)"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>

        <div class="grid grid-cols-7 gap-0.5 text-center">
            <abbr v-for="hari in NAMA_HARI" :key="hari" class="py-1 text-[11px] font-medium text-muted no-underline">
                {{ hari }}
            </abbr>

            <template v-for="(cell, i) in cells" :key="i">
                <span v-if="!cell" class="py-1.5"></span>

                <span
                    v-else
                    class="relative flex h-8 items-center justify-center rounded-lg text-xs tabular-nums transition"
                    :class="
                        cell.key === todayKey
                            ? 'bg-primary font-semibold text-primary-fg'
                            : 'text-content hover:bg-surface-muted'
                    "
                >
                    {{ cell.day }}
                    <span
                        v-if="marked.has(cell.key) && cell.key !== todayKey"
                        class="absolute bottom-1 h-1 w-1 rounded-full bg-primary"
                        aria-hidden="true"
                    ></span>
                </span>
            </template>
        </div>
    </div>
</template>
