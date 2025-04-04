<!-- TODO: maybe make a custom, lighter component. It's not really a breadcrumb. -->

<template>
  <Breadcrumb :model="breadcrumbItems">
    <template #item="{ item }">{{ item.label }}</template>
    <template #separator> / </template>
  </Breadcrumb>
</template>

<script setup lang="ts">
import { Breadcrumb } from "primevue";

const breadcrumbItems = [{ label: "RGPD" }, { label: "Accueil" }];
</script>
