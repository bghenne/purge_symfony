import {createRouter, createWebHistory, Router, RouteRecordRaw} from 'vue-router'

import EligibleObject from "./components/EligibleObject.vue";
import ExcludedObject from "./components/ExcludedObject.vue";
import PurgedObject from "./components/PurgedObject.vue";
import ControlAlert from "./components/ControlAlert.vue";
import PurgeReport from "./components/PurgeReport.vue";

const routes : Array<RouteRecordRaw> = [
    { path: '/eligible-object', component: EligibleObject },
    { path: '/excluded-object', component: ExcludedObject },
    { path: '/purged-object', component: PurgedObject },
    { path: '/control-alert', component: ControlAlert },
    { path: '/purge-report', component: PurgeReport }
]

const router: Router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router;