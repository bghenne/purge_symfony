import {
  createRouter,
  createWebHistory,
  Router,
  RouteRecordRaw,
} from "vue-router";

import AppEligibleObject from "./components/route/AppEligibleObject.vue";
import AppExcludedObject from "./components/route/AppExcludedObject.vue";
import AppPurgedObject from "./components/route/AppPurgedObject.vue";
import AppControlAlert from "./components/route/AppControlAlert.vue";
import AppPurgeReport from "./components/route/AppPurgeReport.vue";
import AppHome from "./components/route/AppHome.vue";
import AppSetting from "./components/route/AppSetting.vue";
import { useRoles } from "./composables/shared/useRoles";
import { Roles } from "./enums/roles";

const { hasRole } = useRoles();

const routes: Array<RouteRecordRaw> = [
  { path: "/", component: AppHome },
  { path: "/eligible-object", component: AppEligibleObject },
  {
    path: "/excluded-object",
    component: AppExcludedObject,
    beforeEnter: () => {
      return hasRole(Roles.ADMIN) || hasRole(Roles.EXCLUSION);
    },
  },
  { path: "/purged-object", component: AppPurgedObject },
  { path: "/control-alert", component: AppControlAlert },
  { path: "/purge-report", component: AppPurgeReport },
  {
    path: "/setting",
    component: AppSetting,
    beforeEnter: () => {
      return hasRole(Roles.ADMIN);
    },
  },
  { path: "/:pathMatch(.*)", redirect: "/" },
];

const router: Router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
