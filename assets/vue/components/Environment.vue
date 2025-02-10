<script setup lang="ts">
import {Identity} from "../types/identity";
import {Ref, ref} from "vue";

let environmentsList : Ref<Array<string>> = ref([]);

if ((localStorage.getItem('identity') as string).length > 0) {
  const identity : Identity = JSON.parse(localStorage.getItem('identity') as string) as Identity;
  environmentsList.value = identity.environments;
}

const environmentValue = defineModel();

</script>

<template>
    <div v-if="environmentsList.length > 0">
      <label>Choisir un environnement</label>
      <select v-model="environmentValue" required="required">
        <option :value="environment" v-for="environment in environmentsList">{{ environment }}</option>
      </select>
    </div>
</template>

<style scoped>

</style>faut