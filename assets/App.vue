<script setup lang="ts">
    import MainMenu from "./vue/components/MainMenu.vue";
    import Header from "./vue/components/Header.vue";

</script>

<template>
  <Header />
  <MainMenu />
  <router-view></router-view>
</template>

<style scoped>

</style>