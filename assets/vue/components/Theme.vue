<script setup lang="ts">
import {onMounted, Ref, ref} from "vue";
import {doRequest} from "../utilities/request";
import {Theme} from "../types/theme";
import {Methods} from "../enums/methods";

const props = defineProps<{
  objectType: string
}>()

let themesList : Ref<Array<string>> = ref([]);


onMounted(() => {
  doRequest(`/api/theme/${props.objectType}`, Methods.GET, {})
      .then((themes: Theme[]) => {
        themesList.value = themes;
      })
      .catch(error => console.log(error))
  });

</script>

<template>
  <div v-if="themesList.length > 0">
    <label>Thème</label>
    <select>
      <option v-for="theme in themesList">{{ theme }}</option>
    </select>
  </div>
</template>

<style scoped>

</style>