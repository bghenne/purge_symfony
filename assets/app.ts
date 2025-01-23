/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

import { createApp } from 'vue'

// Plugins
//import i18n from './plugins/i18n'
//import translations from './i18n.json'

// Directives
//import { autofocus } from './directives/autofocus'

// Internal components
import App from './App.vue'
import router from './vue/router'

// CSS
//import './assets/css/main.css'

createApp(App)
    //.directive('autofocus', autofocus)
    .use(router)
    //.use(i18n, translations)
    .mount('#app');