<template>
  <!-- row 1, column 1–2 -->
  <Header />

  <!-- row 2, column 1 -->
  <!-- TODO: maybe make a custom component. -->
  <ol class="flex ml-8">
    <li>Portail</li>
    <li>Purge</li>
  </ol>

  <!-- row 2, column 2 -->
  <!-- TODO: this must dynamic. Maybe use nested routes and use the router to populate the content. -->
  <!-- TODO: maybe make a custom component. -->
<!--  <Breadcrumb :model="[{ label: 'Formulaire de choix d\'objets' }, { label: 'Liste des objets éligibles' }]" />-->
  <ol class="flex mr-8">
    <li>Formulaire de choix d'objets</li>
    <li>Liste des objets éligibles</li>
  </ol>

  <!-- row 3, column 1 -->
  <div id="layout-column-1" class="ml-8">
    <div class="p-5 pb-7 border border-gray-200">
      <h1 class="text-2xl mb-5">
        Règles de purge
      </h1>
      <div class="grid grid-cols-[auto auto] grid-rows-[auto auto] grid-cols-[min-content] items-center">
        <span class="bi-filetype-pdf text-2xl text-red-700 mr-2" aria-hidden="true"></span>
        Règles générales de purge
        <a class="row-start-2 col-start-2 w-fit text-blue-700 hover:underline" href="#" download>
          Exporter
          <span class="bi-chevron-right text-xs ml-1" aria-hidden="true"></span>
        </a>
      </div>
    </div>
  </div>

  <!-- row 3, column 2 -->
  <main class="p-5 pb-7 border border-gray-200 mr-8">
    <RouterView></RouterView>
  </main>
</template>

<script setup lang="ts">
import Header from "./vue/components/Header.vue";
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
