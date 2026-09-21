import { ref, watchEffect } from 'vue';

const STORAGE_KEY = 'siakad-theme';

function initialDark() {
    if (typeof window === 'undefined') return false;

    const saved = localStorage.getItem(STORAGE_KEY);
    if (saved === 'dark') return true;
    if (saved === 'light') return false;

    return window.matchMedia('(prefers-color-scheme: dark)').matches;
}

const isDark = ref(initialDark());

watchEffect(() => {
    if (typeof document === 'undefined') return;

    document.documentElement.classList.toggle('dark', isDark.value);
    localStorage.setItem(STORAGE_KEY, isDark.value ? 'dark' : 'light');
});

function toggleDark() {
    isDark.value = !isDark.value;
}

export function useDarkMode() {
    return { isDark, toggleDark };
}
