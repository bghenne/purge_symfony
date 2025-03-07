<template>
  <Heading>
    Choix d'accès aux objets
  </Heading>

  <nav>
    <Subheading>Contrôler</Subheading>

    <TiledLink to="/eligible-object">
      Liste des objets éligibles
    </TiledLink>

    <Subheading>Exclure</Subheading>


    <TiledLink to="/excluded-object">
      Liste des objets exclus
    </TiledLink>

    <Subheading>Piloter</Subheading>

    <div class="flex gap-3">
      <TiledLink to="/purged-object">
        Liste des objets purgés
      </TiledLink>
      <TiledLink to="/control-alert">
        Alertes de contrôle
      </TiledLink>
      <TiledLink to="/purge-report">
        Comptes rendus de purge
      </TiledLink>
    </div>

    <Subheading>Paramétrer</Subheading>

    <div class="flex gap-3">
      <TiledLink to="/setting">
        Paramétrer
      </TiledLink>
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
