<script setup lang="ts">

import {ref} from "vue";
import {EligibleObject} from "../types/eligible-object";
import Environment from "../components/Environment.vue";
import Theme from "../components/Theme.vue";
import {ObjectType} from "../enums/object-type";

const eligibleObjects = ref([] as EligibleObject[])

</script>

<template>
  <Environment/>
  <Theme :object-type="ObjectType.ELIGIBLE"/>
  <div v-if="eligibleObjects.length > 0">
    <ul>
      <li v-for="eligibleObject in eligibleObjects">{{ eligibleObject }}</li>
    </ul>
  </div>
</template>

<style scoped>
</style>