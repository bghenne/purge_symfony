<script setup lang="ts">

</script>

<template>
  <h1 class="text-2xl mb-5">
    Choix d'accès aux objets
  </h1>

  <nav>
    <h2 class="w-50 text-center uppercase border-b border-gray-200 p-2 my-4">
      Contrôler
    </h2>
    <RouterLink to="/eligible-object" class="inline-flex justify-center items-center text-center text-base/5 w-50 min-h-20 bg-slate-300 hover:bg-slate-400 p-3">
      Liste des objects éligibles
    </RouterLink>
    <h2 class="w-50 text-center uppercase border-b border-gray-200 p-2 my-4">
      Exclure
    </h2>
    <div>
      <RouterLink to="/excluded-object" class="inline-flex justify-center items-center text-center text-base/5 w-50 min-h-20 bg-slate-300 hover:bg-slate-400 p-3">
        Liste des objects exclus
      </RouterLink>
      <h2 class="w-50 text-center uppercase border-b border-gray-200 p-2 my-4">
        Piloter
      </h2>
      <div class="flex gap-3">
        <RouterLink to="/purged-object" class="inline-flex justify-center items-center text-center text-base/5 w-50 min-h-20 bg-slate-300 hover:bg-slate-400 p-3">
          Liste des objects purgés
        </RouterLink>
        <RouterLink to="/control-alert" class="inline-flex justify-center items-center text-center text-base/5 w-50 min-h-20 bg-slate-300 hover:bg-slate-400 p-3">
          Alerte de contrôle
        </RouterLink>
        <RouterLink to="/purge-report" class="inline-flex justify-center items-center text-center text-base/5 w-50 min-h-20 bg-slate-300 hover:bg-slate-400 p-3">
          Compte-rendu de purge
        </RouterLink>
      </div>
    </div>
  </nav>
</template>

<style scoped>

</style>