<template>
  <!-- Grid row 1, column 1–2 -->
  <AppHeader class="col-span-2" />

  <!-- Grid row 2, column 1 -->
  <AppBreadcrumb class="ml-8" />

  <!-- Grid row 2-3, column 2 -->
  <AppTabs class="row-span-2 mr-8 mb-8" />

  <!-- Grid row 3, column 1 -->
  <div id="advanced-search-form-parent" class="ml-8">
    <AppPurgeRules />
    <!-- The advanced search form is inserted here (with the Teleport component). -->
  </div>
</template>

<script setup lang="ts">
import AppHeader from "./vue/components/compound/AppHeader.vue";
import AppBreadcrumb from "./vue/components/compound/AppBreadcrumb.vue";
import AppPurgeRules from "./vue/components/compound/AppPurgeRules.vue";
import AppTabs from "./vue/components/compound/AppTabs.vue";
</script>

<!--
The application shell is the only component where styles are **not** scoped.

The alternative would be to write them directly in the CSS files, but it's probably better
to keep them here for the sake of "locality of behaviour" and overall consistency.
-->
<style>
@reference "./styles/app.css";

#app {
  /* The application shell is two-dimensional, hence the Grid Layout model. */
  @apply grid
    grid-cols-[minmax(auto,_25rem)_1fr]
    grid-rows-[auto_auto_1fr]
    gap-8;
}

html,
body,
#app {
  /* The grid should cover the whole viewport. The "minimum" ensures that
     the sticky containers (if any) remain sticky whatever the scrolling position. */
  @apply min-h-screen;
}
</style>
