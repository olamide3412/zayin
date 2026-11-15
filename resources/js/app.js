import './bootstrap';
import '../css/app.css';
import { createApp, h } from 'vue';
import { createInertiaApp, Head, Link } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import Layout from './Layouts/Layout.vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';
import { formatDate } from './Utils/dateFormat';
import AOS from 'aos';
import 'aos/dist/aos.css';
import { createPinia } from 'pinia';
import FlashMessages from './Components/FlashMessages.vue';

AOS.init({
    duration: 1000,
    once: true,
});

const pinia = createPinia();

createInertiaApp({
    title: (title) => title ? `${title} | Zayin Ltd` : 'Zayin Ltd | Solar, Starlink & DSTV Installations',
    resolve: async (name) => {
        const page = await resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'));
        if (page.default.layout === undefined) {
            if ((name.startsWith('Auth/') && name !== 'Auth/Login') || name.startsWith('Invoices/')) {
                page.default.layout = null;
            } else {
                page.default.layout = Layout;
            }
        }
        return page;
    },
    setup({ el, App, props, plugin }) {
       const app = createApp({ render: () => h(App, props) });
       app.use(plugin)
          .use(ZiggyVue)
          .use(Toast)
          .use(pinia);

        app.config.globalProperties.$formatDate = formatDate;

        app.component('Head', Head)
            .component('Link', Link)
            .component('FlashMessages', FlashMessages);

        // ✅ Dynamically import FontAwesome to split the chunk
        import('./fontawesome').then(({ FontAwesomeIcon }) => {
            app.component('font-awesome-icon', FontAwesomeIcon)
             app.mount(el);
        });

       // app.mount(el)
    },
    progress: {
        delay: 250,
        color: '#F59E0B',
        includeCSS: true,
        showSpinner: false,
    },
})
