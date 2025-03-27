<template>
  <div v-if="environmentsList.length > 0">
    <label>Choisir un environnement</label>
    <select v-model="environmentValue" required>
      <option v-for="environment in environmentsList" :value="environment">
        {{ environment }}
      </option>
    </select>
  </div>
</template>

<script setup lang="ts">
import { Identity } from "../types/identity";
import { Ref, ref } from "vue";

const environmentsList: Ref<Array<string>> = ref([]);

if ((localStorage.getItem("identity") as string).length > 0) {
  const identity: Identity = JSON.parse(
    localStorage.getItem("identity") as string,
  ) as Identity;
  environmentsList.value = identity.environments;
}

const environmentValue = defineModel({ type: String });
</script>

<style scoped></style>
