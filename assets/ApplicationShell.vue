<script setup lang="ts">
import Header from "./vue/components/Header.vue";
import AdvancedSearch from "./vue/components/AdvancedSearch.vue";
import Navigation from "./vue/components/Navigation.vue";
</script>

<template>
  <!-- row 1, column 1–2 -->
  <Header />

  <!-- row 3, column 1 -->
  <div class="ml-8">
    <div class="p-5 pb-7 border border-gray-200">
      <h1 class="text-2xl mb-5">
        Règles de purge
      </h1>
      <div class="grid grid-cols-[auto auto] grid-rows-[auto auto] items-center">
        <span class="bi-filetype-pdf text-2xl text-red-700 mr-2" aria-hidden="true"></span>
        Règles générales de purge
        <a class="row-start-2 col-start-2 w-fit text-blue-700 hover:underline" href="#" download>
          Exporter
          <span class="bi-chevron-right text-xs ml-1" aria-hidden="true"></span>
        </a>
      </div>
    </div>
    <AdvancedSearch v-if="'/' !== $route.path" />
  </div>

  <!-- row 3, column 2 -->
  <main class="p-5 pb-7 border border-gray-200 mr-8">
    <RouterView></RouterView>
  </main>
</template>

<style>
#app {
  /* The application shell is two-dimensional, hence the Grid Layout model. */
  @apply grid;
  grid-template-columns: auto 1fr;
  grid-template-rows: auto auto 1fr;
  grid-gap: 2rem; /* gap-<number> does not compile (bug?) */
}

html,
body,
#app {
  /* The grid should cover the whole viewport. The "minimum" ensures that
     the sticky containers (if any) remain sticky whatever the scrolling position. */
  @apply min-h-screen;
}
</style>
