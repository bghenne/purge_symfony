<template>
  <h1 :class="$style.heading">
    <slot></slot>
  </h1>
</template>

<script lang="ts" setup></script>

<style module>
@reference "../../styles/app.css";

.heading {
  @apply mb-5
    text-2xl;
}
</style>
