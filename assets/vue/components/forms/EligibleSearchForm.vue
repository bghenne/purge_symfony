<script setup lang="ts">

import {ObjectType} from "../../enums/object-type";
import Theme from "../Theme.vue";
import Environment from "../Environment.vue";
import {ref} from "vue";
import {doRequest} from "../../utilities/request";
import {Methods} from "../../enums/methods";
import {EligibleObject} from "../../types/eligible-object";

const environmentValue = ref('');
const themeValue = ref('');
const emits = defineEmits<{
  update: [eligibleObjects: EligibleObject[]],
  clear: void
}>();

const searchEligible = () => {

  const formData = new FormData;
  formData.append('environment', environmentValue.value);
  formData.append('theme', themeValue.value);

  doRequest('/api/eligible-object', Methods.POST, formData)
      .then((eligibleObjects : EligibleObject[]) => {
        emits('update', eligibleObjects)
      })
      .catch(error => console.log(error))
}

const resetForm = () => {
  environmentValue.value = null;
  themeValue.value = null;
  emits('clear');
}


</script>

<template>
  <form id="search_eligible" @submit.prevent="searchEligible">
    <Environment v-model="environmentValue"/>
    <Theme :object-type="ObjectType.ELIGIBLE" v-model="themeValue" />
    <button type="submit" >Rechercher</button>
    <button type="button" @click="resetForm">Effacer</button>
  </form>
</template>

<style scoped>

</style>