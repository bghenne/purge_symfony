import {
  createRouter,
  createWebHistory,
  Router,
  RouteRecordRaw,
} from "vue-router";

import AppEligibleObject from "./pages/AppEligibleObject.vue";
import AppExcludedObject from "./pages/AppExcludedObject.vue";
import AppPurgedObject from "./pages/AppPurgedObject.vue";
import AppControlAlert from "./pages/AppControlAlert.vue";
import AppPurgeReport from "./pages/AppPurgeReport.vue";
import AppHome from "./pages/AppHome.vue";
import AppSetting from "./pages/AppSetting.vue";
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
