<script setup lang="ts">

import {ref} from "vue";
import {EligibleObject} from "../types/eligible-object";
import EligibleSearchForm from "../components/forms/EligibleSearchForm.vue";


const eligibleObjects = ref([] as EligibleObject[])

const updateEligibleObjects = (newEligibleObjects) => {
  eligibleObjects.value = newEligibleObjects;
}

</script>

<template>
  <EligibleSearchForm @update="(newEligibleObjects => updateEligibleObjects(newEligibleObjects))" />
  <div v-if="eligibleObjects.length > 0">
    <ul>
      <li v-for="eligibleObject in newEligibleObjects">{{ eligibleObject }}</li>
    </ul>
  </div>
</template>

<style scoped>
</style>