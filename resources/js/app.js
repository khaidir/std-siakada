import { createInertiaApp } from '@inertiajs/vue3';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';

/*
 * Glob dibiarkan lazy (tanpa `eager`), sehingga setiap halaman menjadi chunk
 * tersendiri dan hanya diunduh saat dibuka. Dengan `eager: true` seluruh
 * halaman — termasuk Chart.js yang hanya dipakai dashboard — ikut masuk ke
 * bundle awal.
 */
const pages = import.meta.glob('./pages/**/*.vue');

createInertiaApp({
    title: (title) => (title ? `${title} — SIAKAD` : 'SIAKAD'),
    resolve: (name) => {
        // Nama halaman dari controller bisa berbeda kapitalisasi dengan nama file.
        const key =
            Object.keys(pages).find(
                (k) => k.replace(/^\.\/pages\//, '').replace(/\.vue$/, '').toLowerCase() === name.toLowerCase(),
            ) ?? `./pages/${name}.vue`;

        const loader = pages[key];

        if (!loader) {
            throw new Error(`Halaman Inertia tidak ditemukan: ${name}`);
        }

        return loader();
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#c82333',
    },
});
