<template>
  <div v-if="Object.keys(themesList).length > 0">
    <label>Thème</label>
    <select v-model="theme" required>
      <option v-for="(theme, code) in themesList" :value="code">
        {{ theme }}
      </option>
    </select>
  </div>
</template>

<script lang="ts" setup>
import { Ref, ref } from "vue";

const props = defineProps<{
  objectType: string;
}>();

const themesList: Ref<Array<string>> = ref([]);

const theme = defineModel({ type: String });
</script>

<style scoped></style>
