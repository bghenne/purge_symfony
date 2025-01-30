<script setup lang="ts">

import {ref} from "vue";
import {EligibleObject} from "../types/eligible-object";

const eligibleObjects = ref([] as EligibleObject[])

</script>

<template>
  <div v-if="eligibleObjects.length > 0">
    <ul>
      <li v-for="eligibleObject in eligibleObjects">{{ eligibleObject }}</li>
    </ul>
  </div>
</template>

<style scoped>
</style>