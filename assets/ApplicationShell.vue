<script setup lang="ts">
    import Header from "./vue/components/Header.vue";
    import Navigation from "./vue/components/Navigation.vue";
</script>

<template>
  <Header />
  <Navigation />

  <main>
    <router-view></router-view>
  </main>
</template>
