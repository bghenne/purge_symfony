<template>
  <header
    class="col-span-2 flex h-16 border-b border-gray-200 px-4 py-0 text-gray-800"
  >
    <router-link
      to="/"
      class="mr-auto flex items-center px-4 hover:bg-slate-200"
    >
      <span>Open Web <span class="font-bold">Purge</span></span>
    </router-link>

    <div class="mr-4 flex items-center">
      <span class="mr-1">{{ user.firstName }}</span>
      <span class="bi-person text-2xl" aria-hidden="true"></span>
    </div>

    <a class="flex items-center px-4 hover:bg-slate-200" href="/logout">
      <span class="bi-box-arrow-in-left text-2xl"></span>
    </a>
  </header>
</template>

<script setup lang="ts">
import { ref, Ref } from "vue";

import { Identity } from "../../types/identity";
import { doRequest } from "../../utilities/request";
import { Methods } from "../../enums/methods";

const user: Ref<Identity> = ref({} as Identity);

doRequest("/api/get-identity", Methods.GET)
  .then((identity: Identity): void => {
    user.value = identity;
    localStorage.setItem("identity", JSON.stringify(user.value));
  })
  .catch((error: Error): void => {
    alert(`${error.name}: ${error.message}`);
  });
</script>

<style scoped></style>
