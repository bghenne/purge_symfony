import {createRouter, createWebHistory, Router, RouteRecordRaw} from "vue-router";

import EligibleObject from "./pages/EligibleObject.vue";
import ExcludedObject from "./pages/ExcludedObject.vue";
import PurgedObject from "./pages/PurgedObject.vue";
import ControlAlert from "./pages/ControlAlert.vue";
import PurgeReport from "./pages/PurgeReport.vue";
import Home from "./pages/Home.vue";
import Setting from "./pages/Setting.vue";
import {useRoles} from "./composables/shared/useRoles";
import {Roles} from "./enums/roles";

const {getRoles, hasRole} = useRoles();

const routes: Array<RouteRecordRaw> = [
  { path: "/", component: Home },
  { path: "/eligible-object", component: EligibleObject },
  { path: "/excluded-object", component: ExcludedObject,
    beforeEnter : (to, from) => {
      return hasRole(Roles.ADMIN) || hasRole(Roles.EXCLUSION);
    }
},
  { path: "/purged-object", component: PurgedObject },
  { path: "/control-alert", component: ControlAlert },
  { path: "/purge-report", component: PurgeReport },
  { path: "/setting", component: Setting,
    beforeEnter : (to, from) => {
      return hasRole(Roles.ADMIN);
    }
  },
  { path: "/:pathMatch(.*)", redirect: "/" },
];

const router: Router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
