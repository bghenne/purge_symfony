/*
 * JavaScript and CSS entry points.
 * Both are included in the base layout (base.html.twig) by calling Twig helpers from the Encore bundle.
 */

import { createApp } from 'vue'
import router from './vue/router'

import ApplicationShell from './ApplicationShell.vue'
import './styles/app.css';

// Pre-navigates to a URL originating from the middle (i.e. submitted in the browser address bar).
await router.replace({path: document.body.dataset.route as string});

createApp(ApplicationShell)
    .use(router)
    .mount('#app');
