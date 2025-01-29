<script setup lang="ts">
import Header from "./vue/components/Header.vue";
import AdvancedSearch from "./vue/components/AdvancedSearch.vue";
import Navigation from "./vue/components/Navigation.vue";
</script>

<template>
  <Header/>
  <AdvancedSearch/>
  <Navigation/>

  <main>
    <router-view></router-view>
  </main>
</template>
