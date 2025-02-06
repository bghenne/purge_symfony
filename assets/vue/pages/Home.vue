<template>
  <Heading>
    Choix d'accès aux objets
  </Heading>

  <nav>
    <Subheading>
      Contrôler
    </Subheading>
    <TiledLink to="/eligible-object">Liste des objects éligibles</TiledLink>

    <Subheading>
      Exclure
    </Subheading>
    <TiledLink to="/excluded-object">Liste des objects exclus</TiledLink>

    <Subheading>
      Piloter
    </Subheading>
    <div class="flex gap-3">
      <TiledLink to="/purged-object">Liste des objects purgés</TiledLink>
      <TiledLink to="/control-alert">Alerte de contrôle</TiledLink>
      <TiledLink to="/purge-report">Compte-rendu de purge</TiledLink>
    </div>
  </nav>
</template>

<script setup lang="ts">
import TiledLink from "../components/TiledLink.vue";
import Subheading from "../components/Subheading.vue";
import Heading from "../components/Heading.vue";
</script>

<style scoped>
</style>
