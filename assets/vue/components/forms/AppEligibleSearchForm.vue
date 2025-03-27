<template>
  <form id="search_eligible" @submit.prevent="searchEligible">
    <AppEnvironment v-model="environmentValue" />
    <AppTheme v-model="themeValue" :object-type="ObjectType.ELIGIBLE" />
    <button type="submit">Rechercher</button>
    <button type="button" @click="resetForm">Effacer</button>
  </form>
</template>

<script setup lang="ts">
import { ObjectType } from "../../enums/object-type";
import AppTheme from "../AppTheme.vue";
import AppEnvironment from "../AppEnvironment.vue";
import { ref } from "vue";
import { doRequest } from "../../utilities/request";
import { Methods } from "../../enums/methods";
import { EligibleObject } from "../../types/eligible-object";

const environmentValue = ref("");
const themeValue = ref("");
const emits = defineEmits<{
  update: [eligibleObjects: EligibleObject[]];
  clear: void;
}>();

const searchEligible = () => {
  const formData = new FormData();
  formData.append("environment", environmentValue.value);
  formData.append("theme", themeValue.value);

  doRequest("/api/eligible-object", Methods.POST, formData)
    .then((eligibleObjects: EligibleObject[]) => {
      emits("update", eligibleObjects);
    })
    .catch((error) => console.log(error));
};

const resetForm = () => {
  environmentValue.value = null;
  themeValue.value = null;
  emits("clear");
};
</script>

<style scoped></style>
