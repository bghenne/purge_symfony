import {createRouter, createWebHistory, Router, RouteRecordRaw} from 'vue-router'

import EligibleObject from "./pages/EligibleObject.vue";
import ExcludedObject from "./pages/ExcludedObject.vue";
import PurgedObject from "./pages/PurgedObject.vue";
import ControlAlert from "./pages/ControlAlert.vue";
import PurgeReport from "./pages/PurgeReport.vue";
import Navigation from "./components/Navigation.vue";

const routes : Array<RouteRecordRaw> = [
    { path: '/', component: Navigation },
    { path: '/eligible-object', component: EligibleObject },
    { path: '/excluded-object', component: ExcludedObject },
    { path: '/purged-object', component: PurgedObject },
    { path: '/control-alert', component: ControlAlert },
    { path: '/purge-report', component: PurgeReport },
    { path: '/:pathMatch(.*)', redirect: '/' }
];

const router: Router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router;