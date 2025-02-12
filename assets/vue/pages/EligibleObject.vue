<script setup lang="ts">
import { ref } from "vue";
import {EligibleObject, EligibleObjectDetails} from "../types/eligible-object";
import EligibleSearchForm from "../components/forms/EligibleSearchForm.vue";
import { DataTable, Column } from "primevue";

const eligibleObjects = ref([] as EligibleObject[]);

const updateEligibleObjects = (newEligibleObjects: EligibleObject[]) => {
  eligibleObjects.value = newEligibleObjects;
};
</script>

<template>
  <EligibleSearchForm
    @update="(newEligibleObjects) => updateEligibleObjects(newEligibleObjects)"
  />

  <DataTable
    v-if="eligibleObjects.length > 0"
    v-model:expandedRows="eligibleObjects.details"
    dataKey="key"
    :value="eligibleObjects"
    removableSort
    paginator
    :rows="5"
  >
    <Column expander style="width: 5rem" />
    <Column field="familyId" header="Identifiant de famille" sortable></Column>
    <Column field="beneficiaryName" header="Nom" sortable></Column>
    <Column field="beneficiaryFirstname" header="Prénom" sortable></Column>
    <Column field="beneficiaryBirthdate" header="Date de naissance" sortable></Column>
    <Column field="socialSecurityNumber" header="Numéro de Sécurité sociale"></Column>
    <Column field="environment" header="Environnement"></Column>
    <Column field="clientName" header="Nom du client"></Column>
    <template #expansion="slotProps">
      <div class="p-4">
        <p>Délai de conservation : {{ slotProps.data.details.conservationTime }}</p>
        <p>Période d'appel de cotisation : {{ slotProps.data.details.contributionCallPeriod }}</p>
        <p>Année appel de cotisation : {{ slotProps.data.details.contributionCallYear }}</p>
        <p>Date paiement cotisation : {{ slotProps.data.details.contributionPaymentDate }}</p>
        <p>Délai de conservation : {{ slotProps.data.details.contributionTime }}</p>
        <p>Libellé de la règle de purge : {{ slotProps.data.details.purgeRuleLabel }}</p>
      </div>
    </template>
  </DataTable>
</template>

<style scoped></style>