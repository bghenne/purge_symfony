<script setup lang="ts">
import {ref, Ref} from "vue"

import {Identity} from "../types/identity"
import {doRequest} from "../utilities/request"
import {Methods} from "../enums/methods";

let identity: Ref<Identity> = ref({} as Identity);

doRequest('/get-identity', Methods.GET)
    .then((response): void => {
      identity.value = response as Identity;
      localStorage.setItem('identity', JSON.stringify(identity.value));
    })
    .catch((error: Error): void => {
      alert(`${error.name}: ${error.message}`)
    })


</script>

<template>
  <header class="col-span-2 flex text-gray-800 border-b border-gray-200 h-16 py-0 px-4">
    <router-link to="/" class="flex items-center hover:bg-slate-200 px-4 mr-auto">
      <span>Open Web <span class="font-bold">Purge</span></span>
    </router-link>

    <div class="flex items-center mr-4">
      <span class="mr-1">{{ identity.firstName }}</span>
      <span class="bi-person text-2xl" aria-hidden="true"></span>
    </div>

    <a class="flex items-center px-4 hover:bg-slate-200" href="/logout">
      <span class="bi-box-arrow-in-left text-2xl"></span>
    </a>
  </header>
</template>

<style scoped>
</style>
