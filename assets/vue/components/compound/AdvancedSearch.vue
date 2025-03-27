<template>
  <AppSection>
    <AppHeading>Recherche avancée</AppHeading>
    <slot />
  </AppSection>
</template>

<script setup lang="ts">
import AppHeading from "../atomic/AppHeading.vue";
import AppSection from "./AppSection.vue";
</script>

<style scoped></style>
