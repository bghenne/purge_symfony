<script setup lang="ts">

import {ref} from "vue";
import {EligibleObject} from "../types/eligible-object";
import {doRequest} from "../utilities/request";
import {Methods} from "../enums/methods";

const eligibleObjects = ref([] as EligibleObject[])

doRequest('/api/eligible-object', {}, Methods.POST)
    .then(() => {

    })

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