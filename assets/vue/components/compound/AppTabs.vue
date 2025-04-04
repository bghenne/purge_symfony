<template>
  <Tabs v-model:value="currentTab" scrollable @update:value="updateRoute">
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
      <main class="-mt-px border border-gray-200 p-5 pb-7">
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
</template>

<script setup lang="ts">
import { Tab, Tabs, TabList, TabPanel, TabPanels } from "primevue";
import { provide, Ref, ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

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
