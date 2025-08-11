import './bootstrap';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import Vue3Toastify from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import Flowbite from 'flowbite-vue';
import 'flowbite/dist/flowbite.css';

createInertiaApp({
    title: (title) => `${title} - ${import.meta.env.VITE_APP_NAME || 'Laravel'}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(Vue3Toastify, { autoClose: 3000, position: 'top-right' })
            .use(Flowbite)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
