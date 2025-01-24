<script setup lang="ts">

</script>

<template>
    <nav>
      <h2>Contrôler</h2>
      <RouterLink to="/eligible-object">Liste des objects éligibles</RouterLink>
      <h2>Exclure</h2>
      <RouterLink to="/excluded-object">Liste des objects exclus</RouterLink>
      <h2>Piloter</h2>
      <div><RouterLink to="/purged-object">Liste des objects purgés</RouterLink></div>
      <div><RouterLink to="/control-alert">Alerte de contrôle</RouterLink></div>
      <div><RouterLink to="/purge-report">Compte-rendu de purge</RouterLink></div>
    </nav>
</template>

<style scoped>

</style>