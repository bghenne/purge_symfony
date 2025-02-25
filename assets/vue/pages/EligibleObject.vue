<template>

  <div class="card flex justify-center mb-4">
    <Toast/>
    <Form v-slot="$eligibleObjectForm" :resolver="resolver" @submit="onFormSubmit" ref="eligible-object-form">
      <div class="flex gap-5">
        <Select v-model="environment" name="environment" :options="environments" optionLabel="name" placeholder="Choisissez un environnement" fluid checkmark/>
        <Message v-if="$eligibleObjectForm.environment?.invalid" severity="error" size="small" variant="simple">
          {{ $eligibleObjectForm.environment.error.message }}
        </Message>
        <Select v-model="theme" name="theme" :options="themes" optionLabel="name" :loading="fetchingThemes" placeholder="Choisissez un thème" fluid checkmark/>
        <Message v-if="$eligibleObjectForm.theme?.invalid" severity="error" size="small" variant="simple">
          {{ $eligibleObjectForm.theme.error.message }}
        </Message>

        <Teleport to="#layout-column-1" v-if="advancedSearchDisplayed">
          <AdvancedSearch class="mt-4">
            <!-- This 2nd form element is only logically nested in the component, not physically in the DOM
                 (that would be invalid). It leverages the Web platform behaviour for forms submission,
                 e.g. allowing submission from any input by pressing the "enter" key. -->
            <form
                class="grid grid-cols-2 gap-3"
                @submit.prevent="eligibleObjectForm.$el.requestSubmit()"
            >
              <label class="flex flex-col">
                <span class="font-bold">Du</span>
                <DatePicker name="dateFrom" v-model="dateFrom" dateFormat="dd/mm/yy" placeholder="jj/mm/aaaa"/>
                <Message v-if="$eligibleObjectForm.dateFrom?.invalid" severity="error" size="small" variant="simple">
                  {{ $eligibleObjectForm.dateFrom.error.message }}
                </Message>
              </label>
              <label class="flex flex-col">
                <span class="font-bold">Au</span>
                <DatePicker name="dateTo" v-model="dateTo" dateFormat="dd/mm/yy" placeholder="jj/mm/aaaa"/>
                <Message v-if="$eligibleObjectForm.dateTo?.invalid" severity="error" size="small" variant="simple">
                  {{ $eligibleObjectForm.dateTo.error.message }}
                </Message>
              </label>
              <label class="flex flex-col">
                <span class="font-bold">ID de famille</span>
                <InputText name="familyId" v-model="familyId" type="text"/>
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

              <Button type="reset" label="Effacer" severity="secondary" class="row-start-3 shrink-0 mt-4" :disabled="null === dateFrom && null === dateTo && null === familyId"
                      @click="resetAdvancedSearchValues" />
              <Button type="submit" label="Valider" severity="primary" class="row-start-3 shrink-0 mt-4" :disabled="searchInProgress || null === dateFrom && dateTo || dateFrom && null === dateTo" :loading="searchInProgress" />
            </form>
          </AdvancedSearch>
        </Teleport>

        <Button type="reset" label="Effacer" severity="secondary" class="shrink-0" :disabled="null === theme && null === environment"  @click="resetBasicSearchValues" />
        <Button type="submit" label="Rechercher" severity="primary" class="shrink-0" :disabled="searchInProgress || null === theme || null === environment" :loading="searchInProgress" />
      </div>
    </Form>
  </div>

  <DataTable
      ref="eligibleObjectsTable"
      v-model:expandedRows="eligibleObjects.details"
      dataKey="key"
      :value="eligibleObjects"
      :totalRecords="totalRecords" @page="onPage($event)" @sort="onSort($event)"
      removableSort
      paginator
      :lazy="true"
      :rows="10"
      :loading="searchInProgress"
      paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
      currentPageReportTemplate="{first} à {last} sur {totalRecords}"
  >
    <Column expander style="width: 5rem"/>
    <Column field="familyId" header="Identifiant de famille" sortable></Column>
    <Column field="contributionPaymentDate" header="Date de dernier paiment de la cotisation" sortable></Column>
    <Column field="contributionCallPeriod" header="Période des cotisations en mois" sortable></Column>
    <Column field="contributionCallYear" header="Période des cotisations en année" sortable></Column>
    <Column field="conservationTime" header="Délai de conservation appliqué"></Column>
    <Column field="clientName" header="Nom du client"></Column>
    <template #expansion="slotProps">
      <div class="p-4">
        <p>Nom : {{ slotProps.data.details.beneficiaryName }}</p>
        <p>Prénom : {{ slotProps.data.details.beneficiaryFirstname }}</p>
        <p>Numéro de sécurité sociale : {{ slotProps.data.details.socialSecurityNumber }}</p>
        <p>Date de naissance du bénéficiaire : {{ slotProps.data.details.beneficiaryBirthdate }}</p>
      </div>
    </template>
  </DataTable>

</template>

<script setup lang="ts">

import {EligibleObject, EligibleObjects} from "../types/eligible-object";
import {Button, Column, DataTable, DataTablePageEvent, InputText, Message, Select, Toast} from "primevue";
import {Form} from "@primevue/forms";
import {ref, useTemplateRef, watch} from 'vue';
import {useToast} from "primevue/usetoast";
import {ObjectType} from "../enums/object-type";
import {doRequest} from "../utilities/request";
import {Methods} from "../enums/methods";
import {isNumeric} from "../utilities/validation/is-numeric";
import DatePicker from "primevue/datepicker";
import AdvancedSearch from "../components/AdvancedSearch.vue";
import {useEnvironments} from "../composables/shared/useEnvironments";
import {useThemes} from "../composables/shared/useThemes";

const { environments, getEnvironments } = useEnvironments();
const { themes, fetchingThemes, fetchThemes } = useThemes();

const eligibleObjects = ref([] as EligibleObject[]);
const eligibleObjectForm = useTemplateRef('eligible-object-form');

// Basic search state
const environment = ref(null);
const theme = ref(null);

// Advanced search state
const dateFrom = ref(null);
const dateTo = ref(null);
const familyId = ref(null);

const searchInProgress = ref(false);
const totalRecords = ref(0);
const advancedSearchDisplayed = ref(false);

// Store pagination and sort stuffs
const eligibleObjectsTable = ref(null);
const sortField = ref(null);
const sortOrder = ref(null);
const paginationPage = ref(null);

getEnvironments();
fetchThemes(ObjectType.ELIGIBLE);

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
  // SubmitEvent.submitter returns null if a form is submitted programmatically.
  if (null !== originalEvent.submitter) {
    resetAdvancedSearchValues(originalEvent);
    advancedSearchDisplayed.value = false;
    values.dateFrom = undefined;
    values.dateTo = undefined;
    values.familyId = undefined;
  }

  // every time form is submitted, we reset pagination and sort
  resetPaginationAndSort();

  if (valid) {
    toast.add({severity: 'success', summary: 'Recherche en cours.', life: 3000});

    const formData = new FormData;
    formData.append('environment', values.environment.name);
    formData.append('theme', values.theme.code);

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

    findEligibleObjects(formData);

  }
};

const findEligibleObjects = (formData : FormData) => {

  searchInProgress.value = true;

  doRequest('/api/eligible-object', Methods.POST, formData)
      .then((newEligibleObjects: EligibleObjects) => {

        totalRecords.value = newEligibleObjects.total;

        if (newEligibleObjects.total > 0) {
          advancedSearchDisplayed.value = true;
        }

        eligibleObjects.value = newEligibleObjects.eligibleObjects;
      })
      .catch(error => toast.add({severity: 'error', summary: 'Une erreur s\'est produite :' + error, life: 5000}))
      .finally(() => searchInProgress.value = false)

}

const onPage = (event: DataTablePageEvent) => {

  console.log(event)

  const formData = new FormData;
  formData.append('environment', environment.value.name);
  formData.append('theme', theme.value.code);

  if (null !== dateFrom.value) {
    formData.append('dateFrom', dateFrom.value);
  }

  if (null !== dateTo.value) {
    formData.append('dateTo', dateTo.value);
  }

  if (null !== familyId.value) {
    formData.append('familyId', familyId.value);
  }

  const page = 0 === event.page ? event.page : event.page++;
  formData.append('page', page);

  // store pagination
  paginationPage.value = page;

  // look for sort stuffs
  if (null !== sortField.value && null !== sortOrder.value) {
    formData.append('sortField', sortField.value);
    formData.append('sortOrder', sortOrder.value);
  }

  findEligibleObjects(formData);

}

const onSort = (event) => {

  console.log(event)

  const formData = new FormData;
  formData.append('environment', environment.value.name);
  formData.append('theme', theme.value.code);

  if (null !== dateFrom.value) {
    formData.append('dateFrom', dateFrom.value);
  }

  if (null !== dateTo.value) {
    formData.append('dateTo', dateTo.value);
  }

  if (null !== familyId.value) {
    formData.append('familyId', familyId.value);
  }

  // sortField and sortOrder can be null when sort is reset
  // @see https://primevue.org/datatable/#api.datatable.props.removableSort
  // therefore we must remove them from search
  if (null !== event.sortField) {
    formData.append('sortField', event.sortField);
    sortField.value = event.sortField;
  }

  if (null !== event.sortOrder) {
    formData.append('sortOrder', event.sortOrder);
    sortOrder.value = event.sortOrder;
  }

  if (null !== paginationPage.value) {
    formData.append('page', paginationPage.value);
  }

  findEligibleObjects(formData);

}

function resetBasicSearchValues(event: MouseEvent) {
  environment.value = null;
  theme.value = null;

  advancedSearchDisplayed.value = false;
}

function resetAdvancedSearchValues(event: MouseEvent) {
  dateFrom.value = null;
  dateTo.value = null;
  familyId.value = null;
}

function resetPaginationAndSort() {

  // as the data are being reloaded by form submission, we need to tell datatable to "reset" itself
  eligibleObjectsTable.value.d_first = 0;
  eligibleObjectsTable.value.d_page = 1;
  eligibleObjectsTable.value.page = 1;
  eligibleObjectsTable.value.d_sortField = null;
  eligibleObjectsTable.value.d_sortOrder = null;

  // reset fields stored between sort and pagination events
  sortField.value = null;
  sortOrder.value = null;
  paginationPage.value = null;
}

</script>

<style scoped></style>