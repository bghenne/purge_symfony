<template>
  <RouterLink :class="$style.tiledLink" to="">
    <slot></slot>
  </RouterLink>
</template>

<script lang="ts" setup></script>

<style module>
@reference "../../../styles/app.css";

.tiledLink {
  @apply inline-flex
    min-h-20
    w-50
    items-center
    justify-center
    bg-slate-300
    p-3
    text-center
    text-base/5
    hover:bg-slate-400;
}
</style>
