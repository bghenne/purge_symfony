<template>
  <AppHeading> Choix d'accès aux objets</AppHeading>

  <nav>
    <AppSubheading>Contrôler</AppSubheading>

    <AppTiledLink to="/eligible-object">
      Liste des objets éligibles
    </AppTiledLink>

    <AppSubheading>Exclure</AppSubheading>

    <AppTiledLink to="/excluded-object">Liste des objets exclus</AppTiledLink>

    <AppSubheading>Piloter</AppSubheading>

    <div class="flex gap-3">
      <AppTiledLink to="/purged-object">Liste des objets purgés</AppTiledLink>
      <AppTiledLink to="/control-alert">Alertes de contrôle</AppTiledLink>
      <AppTiledLink to="/purge-report">Comptes rendus de purge</AppTiledLink>
    </div>

    <AppSubheading>Paramétrer</AppSubheading>

    <div class="flex gap-3">
      <AppTiledLink to="/setting">Paramétrer</AppTiledLink>
    </div>
  </nav>
</template>

<script lang="ts" setup>
import AppTiledLink from "../atomic/AppTiledLink.vue";
import AppSubheading from "../atomic/AppSubheading.vue";
import AppHeading from "../atomic/AppHeading.vue";
</script>

<style scoped></style>
