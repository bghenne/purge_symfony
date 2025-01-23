<script setup lang="ts">

</script>

<template>
    <nav>
      <h2>Contrôler</h2>
      <router-link to="/eligible-object">Liste des objects éligibles</router-link>
      <h2>Exclure</h2>
      <router-link to="/excluded-object">Liste des objects exclus</router-link>
      <h2>Piloter</h2>
      <div><router-link to="/purged-object">Liste des objects purgés</router-link></div>
      <div><router-link to="/control-alert">Alerte de contrôle</router-link></div>
      <div><router-link to="/purge-report">Compte-rendu de purge</router-link></div>
    </nav>
</template>

<style scoped>

</style>