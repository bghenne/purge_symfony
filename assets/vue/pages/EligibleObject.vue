<template>

  <div class="card flex justify-center mb-4">
    <Toast/>
    <Form v-slot="$eligibleObjectForm" :resolver="resolver" @submit="onFormSubmit" ref="eligible-object-form">
      <div class="flex gap-5">
        <Select name="environment" :options="environments" optionLabel="name" placeholder="Choisissez un environnement"
                fluid checkmark/>
        <Message v-if="$eligibleObjectForm.environment?.invalid" severity="error" size="small" variant="simple">
          {{ $eligibleObjectForm.environment.error.message }}
        </Message>
        <Select name="theme" :options="themes" optionLabel="name" placeholder="Choisissez un thème" fluid checkmark/>
        <Message v-if="$eligibleObjectForm.theme?.invalid" severity="error" size="small" variant="simple">
          {{ $eligibleObjectForm.theme.error.message }}
        </Message>

        <Teleport to="#advanced_search" v-if="eligibleObjects.length > 0">
          <!-- This 2nd form element is only logically nested in the component, not physically in the DOM
               (that would be invalid). It leverages the Web platform behaviour for forms submission,
               e.g. allowing submission from any input by pressing the "enter" key. -->
          <form
              class="grid grid-cols-2 gap-3"
              @submit.prevent="eligibleObjectForm.$el.requestSubmit()"
          >
            <label class="flex flex-col">
              <span class="font-bold">Du</span>
              <DatePicker name="dateFrom" v-model="dateFrom" dateFormat="dd/mm/yy" placeholder="jj/mm/aaaa" />
              <Message v-if="$eligibleObjectForm.dateFrom?.invalid" severity="error" size="small" variant="simple">
                {{ $eligibleObjectForm.dateFrom.error.message }}
              </Message>
            </label>
            <label class="flex flex-col">
              <span class="font-bold">Au</span>
              <DatePicker name="dateTo" v-model="dateTo" dateFormat="dd/mm/yy" placeholder="jj/mm/aaaa" />
              <Message v-if="$eligibleObjectForm.dateTo?.invalid" severity="error" size="small" variant="simple">
                {{ $eligibleObjectForm.dateTo.error.message }}
              </Message>
            </label>
            <label class="flex flex-col">
              <span class="font-bold">ID de famille</span>
              <InputText name="familyId" v-model="familyId" type="text" />
              <Message v-if="$eligibleObjectForm.familyId?.invalid" severity="error" size="small" variant="simple">
                {{ $eligibleObjectForm.familyId.error.message }}
              </Message>
            </label>
<!--            Can be moved in its own page-->
<!--            <div v-if="'/control-alert' === $route.path">-->
<!--              <InputText name="familyId" type="text" placeholder="ID Prestation" />-->
<!--              <Select name="serviceTypologyId" :options="typologies" placeholder="Typlogie de prestation" fluid-->
<!--                      checkmark/>-->
<!--            </div>-->

            <Button type="reset" label="Effacer" severity="secondary" class="row-start-3 shrink-0 mt-4" @click="resetAdvancedSearchValues" v-if="shouldDisplayAdvancedFormButtons" />
            <Button type="submit" label="Valider" severity="primary" class="row-start-3 shrink-0 mt-4" v-if="shouldDisplayAdvancedFormButtons"/>
          </form>
        </Teleport>

        <Button type="reset" label="Effacer" severity="secondary" class="shrink-0" />
        <Button type="submit" label="Rechercher" severity="primary" class="shrink-0" />
      </div>
    </Form>
  </div>

  <DataTable
      v-model:expandedRows="eligibleObjects.details"
      dataKey="key"
      :value="eligibleObjects"
      removableSort
      paginator
      :rows="5"
      :loading="searchInProgress"
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
        <p>Libellé de la règle de purge : {{ slotProps.data.details.purgeRuleLabel }}</p>
      </div>
    </template>
  </DataTable>

</template>

<script setup lang="ts">

import {EligibleObject} from "../types/eligible-object";
import {Button, Column, DataTable, InputText, Message, Select, Toast} from "primevue";
import {Form} from "@primevue/forms";
import {ref, useTemplateRef, watch, watchEffect} from 'vue';
import {useToast} from "primevue/usetoast";
import {fetchEnvironments} from "../composables/environment";
import {ObjectType} from "../enums/object-type";
import {fetchThemes} from "../composables/theme";
import {doRequest} from "../utilities/request";
import {Methods} from "../enums/methods";
import {isNumeric} from "../utilities/validation/is-numeric";
import DatePicker from "primevue/datepicker";

const eligibleObjects = ref([] as EligibleObject[]);
const eligibleObjectForm = useTemplateRef('eligible-object-form');
const environments = ref({});
const themes = ref({});

// Advanced search state
const environment = ref(null);
const theme = ref(null);
const dateFrom = ref(null);
const dateTo = ref(null);
const familyId = ref(null);
const shouldDisplayAdvancedFormButtons = ref(false);
const searchInProgress: boolean = ref(false);

environments.value = fetchEnvironments();
themes.value = fetchThemes(ObjectType.ELIGIBLE);

const toast = useToast();

const typologies: String[] = ref([
  'Audio',
  'Vidéo',
  'Autres'
]);

const resolver = ({values}) => {

  const errors = {};

  if (undefined !== values.familyId && false === isNumeric(values.familyId)) {
    errors.familyId = [{message: "L'id de famille doit être un nombre entier"}];
  }

  return {
    values,
    errors
  }
}

const onFormSubmit = ({originalEvent, valid, values}) => {

  // if form is posted from main search perspective, we reset the advanced one
  if (null !== originalEvent.submitter) {
      resetAdvancedSearchValues();
      values.dateFrom = undefined;
      values.dateTo = undefined;
      values.familyId = undefined;
  }

  if (valid) {
    toast.add({severity: 'success', summary: 'Recherche en cours.', life: 3000});

    searchInProgress.value = true;

    const formData = new FormData;
    formData.append('environment', values.environment.name);
    environment.value = values.environment.name;

    formData.append('theme', values.theme.code);
    theme.value = values.theme.code;

    if (undefined !== values.dateFrom) {
      const convertedDateFrom = String(values.dateFrom).split('(')[0].trim();
      dateFrom.value = convertedDateFrom;
      formData.append('dateFrom', convertedDateFrom);
    }

    if (undefined !== values.dateTo) {
      const convertedDateTo = String(values.dateTo).split('(')[0].trim();
      dateTo.value = convertedDateTo;
      formData.append('dateTo', convertedDateTo);
    }

    if (undefined !== values.familyId) {
      familyId.value = values.familyId;
      formData.append('familyId', values.familyId);
    }

    doRequest('/api/eligible-object', Methods.POST, formData)
        .then((newEligibleObjects: EligibleObject[]) => {
          eligibleObjects.value = newEligibleObjects;
        })
        .catch(error => toast.add({severity: 'error', summary: 'Une erreur s\'est produite :' + error, life: 5000}))
        .finally(() => searchInProgress.value = false)
  }
};

// this watcher will hide search/reset buttons in secondary form if not form field is set
watch([dateFrom, dateTo, familyId], ([newDateFromValue, newDateToValue, newFamilyIdValue]) => {
  shouldDisplayAdvancedFormButtons.value = !(null === newFamilyIdValue
      && null === newDateToValue
      && null === newDateFromValue);
})

function resetAdvancedSearchValues(event) {
  dateFrom.value = null;
  dateTo.value = null;
  familyId.value = null;
}

</script>

<style scoped></style>