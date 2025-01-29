<script setup lang="ts">
import Header from "./vue/components/Header.vue";
import AdvancedSearch from "./vue/components/AdvancedSearch.vue";
import Navigation from "./vue/components/Navigation.vue";
</script>

<template>
  <Header/>
  <div class="m-10">
    <div class="p-5 pb-7 border border-gray-200">
      <h2 class="text-2xl mb-5">
        Règles de purge
      </h2>
      <div class="grid grid-cols-[auto auto] grid-rows-[auto auto] items-center">
        <span class="bi-filetype-pdf text-2xl text-red-700 mr-2" aria-hidden="true"></span>
        Règles générales de purge
        <a class="row-start-2 col-start-2 w-fit text-blue-700 hover:underline" href="#" download>
          Exporter
          <span class="bi-chevron-right text-xs ml-1" aria-hidden="true"></span>
        </a>
      </div>
    </div>
    <AdvancedSearch/>
  </div>
  <Navigation/>

  <main>
    <router-view></router-view>
  </main>
</template>
