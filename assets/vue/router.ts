import { createMemoryHistory, createRouter } from 'vue-router'

import EligibleObject from "./components/EligibleObject.vue";
import ExcludedObject from "./components/ExcludedObject.vue";
import PurgedObject from "./components/PurgedObject.vue";
import ControlAlert from "./components/ControlAlert.vue";
import PurgeReport from "./components/PurgeReport.vue";
import MainMenu from "./components/MainMenu.vue";
import App from "../App.vue";

const routes = [
    { path: '/', component: App },
    { path: '/main', component: MainMenu },
    { path: '/eligible-object', component: EligibleObject },
    { path: '/excluded-object', component: ExcludedObject },
    { path: '/purged-object', component: PurgedObject },
    { path: '/control-alert', component: ControlAlert },
    { path: '/purge-report', component: PurgeReport }
]

const router = createRouter({
    history: createMemoryHistory(),
    routes,
})

export default router;