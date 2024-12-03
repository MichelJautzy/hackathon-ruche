import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import VueLazyLoad from 'vue3-lazyload';

register();

const appName = import.meta.env.VITE_APP_NAME || 'Beard';

function $t(moduleName, key, params = {}, fallback = null) {
    let translation;
    if (window.translations && window.translations[moduleName]) {
        translation = window.translations[moduleName][key] ?? fallback;
    } else {
        translation = fallback ?? key;
    }
    Object.keys(params).forEach(slug => translation = translation.replaceAll(`:${slug}`, params[slug]))
    return translation
}

createInertiaApp({
    title: (title) => title ? `${title} - ${appName}` : appName,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(Notifications)
            .use(ZiggyVue, Ziggy)
            .use(VueLazyLoad, {})
            .use(FloatingVue)
            .use(VueTelInput, {
                mode: 'international',
                defaultCountry: 'fr',
                validCharactersOnly: true,
                dropdownOptions: {showSearchBox: true},
                invalidMessage: 'Numéro de téléphone invalide',
            })
            .directive('click-outside', {
                beforeMount(el, binding) {
                    el.clickOutsideEvent  = function(event) {
                        if (! (el === event.target || el.contains(event.target))) {
                                binding.value(event, el);
                        }
                    };
                    setTimeout(() => {
                        document.body.addEventListener('click', el.clickOutsideEvent );
                    }, 100);
                },
                unmounted(el) {
                    document.body.removeEventListener('click', el.clickOutsideEvent );
                },
            })
            .mixin({ methods: { $t } })
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
