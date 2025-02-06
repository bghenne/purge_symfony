<template>
  <h2 :class="$style.subheading">
    <slot></slot>
  </h2>
</template>

<script lang="ts" setup></script>

<style module>
@reference "../../styles/app.css";

.subheading {
  @apply
    my-4
    w-50
    border-b
    border-gray-200
    p-2
    text-center
    uppercase
  ;
}
</style>
