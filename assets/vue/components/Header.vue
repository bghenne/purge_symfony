<script setup lang="ts">
import {ref, Ref} from "vue"

import {Identity} from "../types/identity"
import {doRequest} from "../utilities/request"

let identity: Ref<Identity> = ref({} as Identity);

doRequest('/get-identity', {}, 'GET')
    .then((response): void => {
      identity.value = response as Identity;
    })
    .catch((error: Error): void => {
      alert(`${error.name}: ${error.message}`)
    })
</script>

<template>
  <header class="col-start-1 col-span-2 flex items-center text-gray-800 border-b border-gray-200 py-4 px-8">
    <div class="mr-auto">
      <router-link to="/">Open Web <span class="font-bold">Purge</span></router-link>
    </div>

    <div class="flex items-center mr-5">
      <span class="mr-1">{{ identity.firstName }}</span>
      <span class="bi-person text-2xl" aria-hidden="true"></span>
    </div>

    <a href="/logout">
      <span class="bi-box-arrow-in-left text-2xl"></span>
    </a>
  </header>
</template>

<style scoped>
</style>
