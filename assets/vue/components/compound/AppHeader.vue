<!--
The header displays the app logo centered relative to the viewport,
and other content (user name, etc.) aligned to the right.

That is done with elements on the sides that both grow (flex-grow:1) with the same growth factor
and have an equal width (flex-basis:0, which replaces the default value "auto").
-->
<template>
  <header
    class="flex items-center border-b border-gray-200 px-8 py-6 text-gray-800 before:grow-1"
  >
    <img src="/assets/images/open.svg" alt="Open by heka" class="h-17" />

    <div class="flex flex-1 items-center justify-end">
      <span class="mr-1">{{ user.firstName }} </span>
      <span class="bi-person mr-2 text-2xl" aria-hidden="true"></span>

      <a class="flex items-center p-2 hover:bg-slate-200" href="/logout">
        <span
          class="bi-box-arrow-in-left text-2xl"
          aria-label="Se déconnecter"
        ></span>
      </a>
    </div>
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
