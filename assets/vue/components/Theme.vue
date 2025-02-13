<script setup lang="ts">
import {onMounted, Ref, ref} from "vue";
import {doRequest} from "../utilities/request";
import {Theme} from "../types/theme";
import {Methods} from "../enums/methods";

const props = defineProps<{
  objectType: string
}>()

let themesList : Ref<Array<string>> = ref([]);

const theme = defineModel();

</script>

<template>
  <div v-if="Object.keys(themesList).length > 0">
    <label>Thème</label>
    <select v-model="theme" required="required">
      <option :value="code" v-for="(theme, code) in themesList">{{ theme }}</option>
    </select>
  </div>
</template>

<style scoped>

</style>