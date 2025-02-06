<template>
  <section :class="$style.section">
    <slot></slot>
  </section>
</template>

<script lang="ts" setup></script>

<style module>
@reference "../../styles/app.css";

.section {
  @apply
    border
    border-gray-200
    p-5
    pb-7
  ;
}
</style>
