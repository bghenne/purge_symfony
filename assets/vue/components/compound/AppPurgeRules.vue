<template>
  <AppSection>
    <AppHeading>Règles de purge</AppHeading>
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
  </AppSection>
</template>

<script setup lang="ts">
import AppHeading from "./../atomic/AppHeading.vue";
import AppSection from "./AppSection.vue";
</script>

<style>
@reference "./../../../styles/app.css";
</style>
