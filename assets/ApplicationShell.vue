<template>
  <!-- row 1, column 1–2 -->
  <AppHeader />

  <!-- row 2, column 1 -->
  <!-- TODO: maybe make a custom component. -->
  <ol class="ml-8 flex">
    <li>Portail</li>
    <li>Purge</li>
  </ol>

  <!-- row 2, column 2 -->
  <!-- TODO: this must dynamic. Maybe use nested routes and use the router to populate the content. -->
  <!-- TODO: maybe make a custom component. -->
  <!--  <Breadcrumb :model="[{ label: 'Formulaire de choix d\'objets' }, { label: 'Liste des objets éligibles' }]" />-->
  <ol class="mr-8 flex">
    <li>Formulaire de choix d'objets</li>
    <li>Liste des objets éligibles</li>
  </ol>

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

  <!-- row 3, column 2 -->
  <main class="mr-8 border border-gray-200 p-5 pb-7">
    <RouterView />
  </main>
</template>

<script setup lang="ts">
import AppHeader from "./vue/components/compound/AppHeader.vue";
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
