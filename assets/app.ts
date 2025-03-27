/*
 * JavaScript and CSS entry points.
 * Both are included in the base layout (base.html.twig) by calling Twig helpers from the Vite bundle.
 */

import { createApp } from "vue";
import PrimeVue from "primevue/config";
import Aura from "@primevue/themes/aura";
import router from "./vue/router";

import ApplicationShell from "./ApplicationShell.vue";
import "./styles/app.css";
import "bootstrap-icons/font/bootstrap-icons.css";
import { ToastService } from "primevue";

// Pre-navigates to a URL that comes from the middle (i.e. submitted in the browser address bar).
await router.replace({ path: document.body.dataset.route as string });

createApp(ApplicationShell)
  .use(router)
  .use(PrimeVue, {
    theme: {
      preset: Aura,
      options: {
        darkModeSelector: false,
      },
    },
  })
  .use(ToastService)
  .mount("#app");
