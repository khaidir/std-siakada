import { createInertiaApp } from '@inertiajs/vue3';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';

createInertiaApp({
    title: (title) => (title ? `${title} — SIAKAD` : 'SIAKAD'),
    resolve: (name) => {
        const pages = import.meta.glob('./pages/**/*.vue', { eager: true });
        // Case-insensitive lookup: Vite glob preserves filesystem casing
        // but Inertia page names from controller are lowercase
        const key = Object.keys(pages).find(k =>
            k.replace(/^\.\/pages\//, '').replace(/\.vue$/, '').toLowerCase() === name.toLowerCase()
        );
        return key ? pages[key] : pages[`./pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4f46e5',
    },
});

