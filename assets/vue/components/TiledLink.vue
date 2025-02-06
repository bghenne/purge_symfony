<script setup lang="ts">
</script>

<template>
  <RouterLink to="" :class="$style.tiledLink">
    <slot ></slot>
  </RouterLink>
</template>

<style module>
@reference "../../styles/app.css";

.tiledLink {
  @apply
    inline-flex justify-center items-center text-center text-base/5 w-50 min-h-20 bg-slate-300 hover:bg-slate-400 p-3
  ;
}
</style>
