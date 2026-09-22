import { computed, ref, watch } from 'vue';
import { useDarkMode } from '@/composables/useDarkMode';

/**
 * Membaca warna chart dari token CSS di app.css sehingga grafik memakai palet
 * yang sama dengan sisa antarmuka dan ikut berubah saat tema gelap aktif.
 */
function readVar(name, fallback) {
    if (typeof window === 'undefined') return fallback;

    const value = getComputedStyle(document.documentElement).getPropertyValue(name).trim();

    return value || fallback;
}

export function useChartTheme() {
    const { isDark } = useDarkMode();

    // Dipakai sebagai pemicu agar warna dibaca ulang setiap tema berganti.
    const revision = ref(0);
    watch(isDark, () => {
        // Tunggu satu frame supaya kelas `.dark` sudah terpasang saat nilai dibaca.
        requestAnimationFrame(() => (revision.value += 1));
    });

    const palette = computed(() => {
        void revision.value;

        return {
            series: [
                readVar('--chart-1', '#c82333'),
                readVar('--chart-2', '#2563eb'),
                readVar('--chart-3', '#16a34a'),
                readVar('--chart-4', '#d97706'),
                readVar('--chart-5', '#7c3aed'),
                readVar('--chart-6', '#0891b2'),
            ],
            primary: readVar('--primary', '#c82333'),
            content: readVar('--content', '#1f2933'),
            muted: readVar('--muted', '#6b7280'),
            border: readVar('--border', '#e5e7eb'),
            surface: readVar('--surface', '#ffffff'),
        };
    });

    return { palette, isDark };
}
