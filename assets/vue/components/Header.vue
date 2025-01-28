<script setup lang="ts">
import { ref, Ref } from "vue"

import { Identity } from "../types/identity"
import { doRequest } from "../utilities/request"

let identity: Ref<Identity> = ref({} as Identity);

doRequest('/get-identity', {}, 'GET')
    .then((response): void => {
      identity.value = response as Identity;
    })
    .catch((error: Error): void => {
      alert(`${error.name}: ${error.message}`)
    })

function logout(): void {
  window.location.assign('/logout');
}
</script>

<template>
<header class="col-start-1 col-span-2 flex items-center">
  <div class="mr-auto">
    <router-link to="/">Open Web <span class="font-bold">Purge</span></router-link>
  </div>

  <div class="mr-2">
    {{ identity.firstName }} {{  identity.surName }}
  </div>

  <button @click="logout">
    Déconnexion
  </button>
</header>
</template>

<style scoped>
</style>
