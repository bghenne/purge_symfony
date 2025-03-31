<template>
  <!-- row 1, column 1–2 -->
  <AppHeader />

  <!-- row 2, column 1 -->
  <!-- TODO: maybe make a custom, lighter component. It's not really a breadcrumb. -->
  <Breadcrumb :model="breadcrumbItems" class="ml-8">
    <template #item="{ item }">{{ item.label }}</template>
    <template #separator> / </template>
  </Breadcrumb>

  <!-- row 2-3, column 2 -->
  <Tabs
    v-model:value="currentTab"
    class="row-span-2 min-w-full"
    scrollable
    @update:value="updateRoute"
  >
    <TabList class="mb-8">
      <!-- One tab equals one route. -->
      <template v-for="tab in tabs" :key="tab.title">
        <Tab v-if="tab.visible" :value="tab.value">
          {{ tab.title }}
        </Tab>
      </template>
    </TabList>
    <TabPanels
      :pt="{
        root: '!p-0',
      }"
    >
      <!-- Only one tab panel has to be rendered at a time. -->
      <!-- The negative top-margin is here to cancel out the border width. -->
      <main class="-mt-px mr-8 border border-gray-200 p-5 pb-7">
        <TabPanel :value="currentTab">
          <RouterView v-slot="{ Component }">
            <!-- Route components must not be unmounted. Only the end-user may reset their state. -->
            <!-- https://router.vuejs.org/guide/advanced/router-view-slot.html#KeepAlive-Transition -->
            <KeepAlive>
              <Component :is="Component" />
            </KeepAlive>
          </RouterView>
        </TabPanel>
      </main>
    </TabPanels>
  </Tabs>

  <!-- row 3, column 1 -->
  <div id="layout-column-1" class="ml-8">
    <div class="border border-gray-200 p-5 pb-7">
      <h1 class="mb-5 text-2xl">Règles de purge</h1>
      <div
        class="grid-cols-[auto auto] grid-rows-[auto auto] grid grid-cols-[min-content] items-center"
      >
        <span
          class="bi-filetype-pdf mr-2 text-2xl text-red-700"
          aria-hidden="true"
        />
        Règles générales de purge
        <a
          class="col-start-2 row-start-2 w-fit text-blue-700 hover:underline"
          href="#"
          download
        >
          Exporter
          <span class="bi-chevron-right ml-1 text-xs" aria-hidden="true" />
        </a>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import AppHeader from "./vue/components/compound/AppHeader.vue";
import { Breadcrumb, Tab, Tabs, TabList, TabPanel, TabPanels } from "primevue";
import { provide, Ref, ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

const breadcrumbItems = [{ label: "RGPD" }, { label: "Accueil" }];

type Tab = {
  title: string;
  path: string;
  value: string;
  visible: boolean;
};

// A subset of routes with additional properties (can be improved).
const tabs: Ref<Tab[]> = ref([
  {
    title: "Formulaire de choix d'objets",
    path: "/",
    value: "0",
    visible: true,
  },
  {
    title: "Liste des objets éligibles",
    path: "/eligible-object",
    value: "1",
    visible: false,
  },
  {
    title: "Liste des objets exclus",
    path: "/excluded-object",
    value: "2",
    visible: false,
  },
  {
    title: "Liste des objets purgés",
    path: "/purged-object",
    value: "3",
    visible: false,
  },
  {
    title: "Alertes de contrôle",
    path: "/control-alert",
    value: "4",
    visible: false,
  },
  {
    title: "Comptes rendus de purge",
    path: "/purge-report",
    value: "5",
    visible: false,
  },
  {
    title: "Paramétrer",
    path: "/setting",
    value: "6",
    visible: false,
  },
]);
const currentTab = ref("0");

/**
 * Navigates to the route that matches the selected tab.
 */
function updateRoute(event: string | number) {
  router.push(getSelectedTab(Number(event)).path);
}

/**
 * Updates both the current tab and its content with the relevant route component.
 *
 * Called from onActivated() lifecycle hooks. onMounted() is inapplicable,
 * because route components are cached with KeepAlive.
 */
function updateTabs(value: string) {
  currentTab.value = value;
  getSelectedTab(Number(value)).visible = true;
}

/**
 * Returns the Tab object that matches the selected tab index.
 */
function getSelectedTab(index: number): Tab {
  return tabs.value[Number(index)];
}

provide("updateTabs", updateTabs);
</script>

<style>
#app {
  /* The application shell is two-dimensional, hence the Grid Layout model. */
  @apply grid;
  grid-template-columns: minmax(auto, 25rem) 1fr;
  grid-template-rows: auto auto 1fr;
  grid-gap: 2rem;
}

html,
body,
#app {
  /* The grid should cover the whole viewport. The "minimum" ensures that
     the sticky containers (if any) remain sticky whatever the scrolling position. */
  @apply min-h-screen;
}
</style>
