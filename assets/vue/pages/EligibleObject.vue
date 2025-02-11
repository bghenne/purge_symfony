<script setup lang="ts">
import { ref } from "vue";
import {EligibleObject, EligibleObjectDetails} from "../types/eligible-object";
import EligibleSearchForm from "../components/forms/EligibleSearchForm.vue";
import { DataTable, Column } from "primevue";

const eligibleObjects = ref([] as EligibleObject[]);
const eligibleObjectsDetails = ref([] as EligibleObjectDetails[]);

const updateEligibleObjects = (newEligibleObjects: EligibleObject[]) => {
  for (const [key, newEligibleObject] of Object.entries(newEligibleObjects)) {
    newEligibleObject['key'] = key;
    newEligibleObject['details'] = {
      key: key,
      dateCampagne: newEligibleObject.dateCampagne,
      nomDuClient: newEligibleObject.nomDuClient,
      environnement: newEligibleObject.environnement,
      datePaiementCotisation: newEligibleObject.datePaiementCotisation,
      periodeAppelCotisation: newEligibleObject.periodeAppelCotisation,
      anneeAppelCotisation: newEligibleObject.anneeAppelCotisation,
      delaiConservation: newEligibleObject.delaiConservation,
      libRegPurg: newEligibleObject.libRegPurg,
    };
  }
  console.log(eligibleObjectsDetails.value);
  eligibleObjects.value = newEligibleObjects;
  console.log(eligibleObjects.value);
};
</script>

<template>
  <EligibleSearchForm
    @update="(newEligibleObjects) => updateEligibleObjects(newEligibleObjects)"
  />

  <DataTable
    v-if="eligibleObjects.length > 0"
    v-model:expandedRows="eligibleObjectsDetails"
    dataKey="key"
    :value="eligibleObjects"
    removableSort
    paginator
    :rows="5"
  >
    <Column expander style="width: 5rem" />
    <Column field="identifiantFamille" header="Identifiant de famille" sortable></Column>
    <Column field="nomBeneficiaire" header="Nom" sortable></Column>
    <Column field="prenomBeneficiaire" header="Prénom" sortable></Column>
    <Column field="dateNaissanceBeneficiaire" header="Date de naissance" sortable></Column>
    <Column field="numeroSecuriteSociale" header="Numéro de Sécurité sociale"></Column>
    <template #expansion="slotProps">
      <div class="p-4">
        {{ slotProps.data.details.nomDuClient }}
      </div>
    </template>
  </DataTable>
</template>

<style scoped></style>