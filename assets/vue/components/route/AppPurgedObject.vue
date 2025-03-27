<template>
  <div class="card mb-4 flex justify-center">
    <Toast />
    <Form
      v-slot="$purgedObjectForm"
      ref="purged-object-form"
      :resolver="resolver"
      @submit="onFormSubmit"
    >
      <div class="flex gap-5">
        <Select
          ref="environment-select"
          v-model="environment"
          name="environment"
          :options="environments"
          option-label="name"
          placeholder="Choisissez un environnement"
          fluid
          checkmark
        />
        <Message
          v-if="$purgedObjectForm.environment?.invalid"
          severity="error"
          size="small"
          variant="simple"
        >
          {{ $purgedObjectForm.environment.error.message }}
        </Message>
        <Select
          v-model="theme"
          name="theme"
          :options="themes"
          option-label="name"
          :loading="fetchingThemes"
          placeholder="Choisissez un thème"
          fluid
          checkmark
        />
        <Message
          v-if="$purgedObjectForm.theme?.invalid"
          severity="error"
          size="small"
          variant="simple"
        >
          {{ $purgedObjectForm.theme.error.message }}
        </Message>

        <Teleport v-if="advancedSearchDisplayed" to="#layout-column-1">
          <AdvancedSearch class="mt-4">
            <!-- This 2nd form element is only logically nested in the component, not physically in the DOM
                 (that would be invalid). It leverages the Web platform behaviour for forms submission,
                 e.g. allowing submission from any input by pressing the "enter" key. -->
            <form
              class="grid grid-cols-2 gap-3"
              @submit.prevent="purgedObjectForm.$el.requestSubmit()"
            >
              <label class="flex flex-col">
                <span class="font-bold">Du</span>
                <DatePicker
                  v-model="dateFrom"
                  name="dateFrom"
                  date-format="dd/mm/yy"
                  placeholder="jj/mm/aaaa"
                  :disabled="familyId"
                />
                <Message
                  v-if="$purgedObjectForm.dateFrom?.invalid"
                  severity="error"
                  size="small"
                  variant="simple"
                >
                  {{ $purgedObjectForm.dateFrom.error.message }}
                </Message>
              </label>
              <label class="flex flex-col">
                <span class="font-bold">Au</span>
                <DatePicker
                  v-model="dateTo"
                  name="dateTo"
                  date-format="dd/mm/yy"
                  placeholder="jj/mm/aaaa"
                  :disabled="familyId"
                />
                <Message
                  v-if="$purgedObjectForm.dateTo?.invalid"
                  severity="error"
                  size="small"
                  variant="simple"
                >
                  {{ $purgedObjectForm.dateTo.error.message }}
                </Message>
              </label>
              <label class="flex flex-col">
                <span class="font-bold">ID de famille</span>
                <InputText
                  v-model="familyId"
                  name="familyId"
                  :disabled="dateFrom || dateTo"
                />
                <Message
                  v-if="$purgedObjectForm.familyId?.invalid"
                  severity="error"
                  size="small"
                  variant="simple"
                >
                  {{ $purgedObjectForm.familyId.error.message }}
                </Message>
              </label>
              <Button
                type="reset"
                label="Effacer"
                severity="secondary"
                class="row-start-3 mt-4 shrink-0"
                :disabled="
                  null === dateFrom && null === dateTo && null === familyId
                "
                @click="resetAdvancedSearchValues"
              />
              <Button
                type="submit"
                label="Valider"
                severity="primary"
                class="row-start-3 mt-4 shrink-0"
                :disabled="
                  searchInProgress ||
                  (null === dateFrom && dateTo) ||
                  (dateFrom && null === dateTo) ||
                  (null === dateFrom && null === dateTo && null === familyId)
                "
                :loading="searchInProgress"
              />
              <Button
                type="button"
                label="Exporter votre filtre"
                severity="secondary"
                class="shrink-0"
                :disabled="!advancedSearchDone || purgedObjects.length <= 0"
                @click="onExport"
              />
            </form>
          </AdvancedSearch>
        </Teleport>

        <Button
          type="reset"
          label="Effacer"
          severity="secondary"
          class="shrink-0"
          :disabled="
            searchInProgress || (null === theme && null === environment)
          "
          @click="resetBasicSearchValues"
        />
        <Button
          type="submit"
          label="Rechercher"
          severity="primary"
          class="shrink-0"
          :disabled="searchInProgress || null === theme || null === environment"
          :loading="searchInProgress"
        />
        <Button
          type="button"
          label="Exporter toute la liste"
          severity="secondary"
          class="shrink-0"
          :disabled="searchInProgress || purgedObjects.length <= 0"
          @click="onExport"
        />
      </div>
    </Form>
  </div>

  <DataTable
    ref="purgedObjectsTable"
    v-model:expanded-rows="purgedObjects.details"
    data-key="key"
    :value="purgedObjects"
    :total-records="totalRecords"
    removable-sort
    paginator
    :lazy="true"
    :rows="10"
    :loading="searchInProgress"
    paginator-template="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
    current-page-report-template="{first} à {last} sur {totalRecords}"
    @page="onPage($event)"
    @sort="onSort($event)"
  >
    <Column expander style="width: 5rem" />
    <Column
      ref="first-table-header"
      field="familyId"
      header="Identifiant de famille"
      sortable
    ></Column>
    <Column
      field="contributionPaymentDate"
      header="Date de dernier paiment de la cotisation"
      sortable
    ></Column>
    <Column
      field="contributionCallPeriod"
      header="Période des cotisations en mois"
      sortable
    ></Column>
    <Column
      field="contributionCallYear"
      header="Période des cotisations en année"
      sortable
    ></Column>
    <Column
      field="conservationTime"
      header="Délai de conservation appliqué"
    ></Column>
    <Column field="clientName" header="Nom du client"></Column>
    <template #expansion="slotProps">
      <div class="p-4">
        <p>Nom : {{ slotProps.data.details.beneficiaryName }}</p>
        <p>Prénom : {{ slotProps.data.details.beneficiaryFirstname }}</p>
        <p>
          Numéro de sécurité sociale :
          {{ slotProps.data.details.socialSecurityNumber }}
        </p>
        <p>
          Date de naissance du bénéficiaire :
          {{ slotProps.data.details.beneficiaryBirthdate }}
        </p>
      </div>
    </template>
  </DataTable>
</template>

<script setup lang="ts">
import { purgedObjects } from "../types/purged-object";
import {
  Button,
  Column,
  DataTable,
  DataTablePageEvent,
  DataTableSortEvent,
  DataTableState,
  InputText,
  Message,
  Select,
  Toast,
} from "primevue";
import { Form } from "@primevue/forms";
import { onMounted, ref, useTemplateRef } from "vue";
import { useToast } from "primevue/usetoast";
import { ObjectType } from "../../enums/object-type";
import { doRequest } from "../../utilities/request";
import { Methods } from "../../enums/methods";
import { isNumeric } from "../../utilities/validation/is-numeric";
import DatePicker from "primevue/datepicker";
import AdvancedSearch from "../compound/AdvancedSearch.vue";
import { useEnvironments } from "../../composables/shared/useEnvironments";
import { useThemes } from "../../composables/shared/useThemes";

const { environments, getEnvironments } = useEnvironments();
const { themes, fetchingThemes, fetchThemes } = useThemes();

const purgedObjects = ref([]);
const purgedObjectForm = useTemplateRef("purged-object-form");

const environmentSelect = useTemplateRef("environment-select");
const firstTableHeader = useTemplateRef("first-table-header");

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
const advancedSearchDone = ref(false);

// To use the pagination and sorting features along with lazy loading,
// we need to read and update the DataTable internal state (a limitation of PrimeVue).
const purgedObjectsTable = ref({} as DataTableState);

// As of PrimeVue 4.2.5, the DataTable component state does not include
// the current page, which must be tracked here.
const paginationPage = ref(0);

getEnvironments();
fetchThemes(ObjectType.PURGED);

const toast = useToast();

onMounted(async () => {
  // This is a workaround. Currently, many PrimeVue components do not expose a function
  // to set the focus on the underlying interactive element.
  // See https://github.com/primefaces/primevue/issues/3138.
  environmentSelect.value.$refs.focusInput.focus();
});

const resolver = ({ values }) => {
  const errors = {};

  if (undefined !== values.familyId && false === isNumeric(values.familyId)) {
    errors.familyId = [
      { message: "L'id de famille doit être un nombre entier" },
    ];
  }

  if (values.familyId < 1 || values.familyId > 9999999999) {
    errors.familyId = [
      {
        message:
          "L'id de famille doit être un nombre entier compris entre 1 et 9999999999",
      },
    ];
  }

  return {
    values,
    errors,
  };
};

const onFormSubmit = ({ originalEvent, valid, values }) => {
  // if form is posted from main search perspective, we reset the advanced one
  // SubmitEvent.submitter returns null if a form is submitted programmatically.
  if (null !== originalEvent.submitter) {
    resetAdvancedSearchValues(originalEvent);
    advancedSearchDisplayed.value = false;
    values.dateFrom = null;
    values.dateTo = null;
    values.familyId = null;
  } else {
    // secondary export button is no more disabled once advanced search is done
    advancedSearchDone.value = true;
  }

  // every time form is submitted, we reset pagination and sort
  // TODO: maybe call this only if the request is successful, and sets the data to null if it is not
  resetPaginationAndSort();

  if (valid) {
    const formData = new FormData();
    formData.append("environment", values.environment.name);
    formData.append("theme", values.theme.code);

    if (null !== values.dateFrom) {
      const convertedDateFrom = String(values.dateFrom).split("(")[0].trim();
      formData.append("dateFrom", convertedDateFrom);
    }

    if (null !== values.dateTo) {
      const convertedDateTo = String(values.dateTo).split("(")[0].trim();
      formData.append("dateTo", convertedDateTo);
    }

    if (null !== values.familyId) {
      familyId.value = values.familyId;
      formData.append("familyId", values.familyId);
    }

    findPurgedObjects(formData);
  }
};

const findPurgedObjects = (formData: FormData) => {
  searchInProgress.value = true;

  doRequest("/api/purged-object", Methods.POST, formData)
    .then(async (newPurgedObjects: purgedObjects) => {
      totalRecords.value = newPurgedObjects.total;
      purgedObjects.value = newPurgedObjects.purgedObjects;

      if (newPurgedObjects.total > 0) {
        advancedSearchDisplayed.value = true;

        // Ugly workaround to set the focus on the header of the 1st sortable column.
        // The usual hacks ($el and $refs) seem inapplicable with the Column component.
        (
          document.querySelector("th[tabindex]") as HTMLTableCellElement
        ).focus();
      } else {
        environmentSelect.value.$refs.focusInput.focus();
      }
    })
    .catch((error) =>
      toast.add({
        severity: "error",
        summary: "Une erreur s'est produite :" + error,
        life: 5000,
      }),
    )
    .finally(() => (searchInProgress.value = false));
};

const findPurgedObjectsToExport = (formData: FormData) => {
  let url: string = "/api/purged-object/export?";

  for (const value of formData.entries()) {
    url += value[0] + "=" + value[1] + "&";
  }

  window.open(url);
};

const onPage = (event: DataTablePageEvent) => {
  const formData = new FormData();
  formData.append("environment", environment.value.name);
  formData.append("theme", theme.value.code);

  if (null !== dateFrom.value) {
    formData.append("dateFrom", dateFrom.value);
  }

  if (null !== dateTo.value) {
    formData.append("dateTo", dateTo.value);
  }

  if (null !== familyId.value) {
    formData.append("familyId", familyId.value);
  }

  formData.append("page", String(event.page));

  // The current page is not included in the DataTable component state.
  // So in order to access it and restore it in the onSort callback
  // (which otherwise resets it), it must first be set here.
  paginationPage.value = event.page;

  // Checks if sorting criteria must be passed to the webservice.
  // Note that the sorting criteria are not available in DataTablePageEvent.
  // The workaround is to retrieve them from the component state.
  if (undefined !== purgedObjectsTable.value.d_sortField) {
    formData.append(
      "sortField",
      purgedObjectsTable.value.d_sortField as string,
    );
    formData.append("sortOrder", String(purgedObjectsTable.value.d_sortOrder));
  }

  findPurgedObjects(formData);
};

const onSort = (event: DataTableSortEvent) => {
  const formData = new FormData();
  formData.append("environment", environment.value.name);
  formData.append("theme", theme.value.code);

  if (null !== dateFrom.value) {
    formData.append("dateFrom", dateFrom.value);
  }

  if (null !== dateTo.value) {
    formData.append("dateTo", dateTo.value);
  }

  if (null !== familyId.value) {
    formData.append("familyId", familyId.value);
  }

  // sortField and sortOrder evaluate to null when a sorted column becomes unsorted again.
  // @see https://primevue.org/datatable/#api.datatable.props.removableSort
  // therefore we must remove them from search
  if (null !== event.sortField) {
    formData.append("sortField", event.sortField as string);
  }

  if (null !== event.sortOrder) {
    formData.append("sortOrder", String(event.sortOrder));
  }

  formData.append("page", String(paginationPage.value));

  // This prevents the current page from being reset.
  purgedObjectsTable.value.d_first =
    paginationPage.value * purgedObjectsTable.value.d_rows;

  findpurgedObjects(formData);
};

const onExport = () => {
  const formData = new FormData();
  formData.append("environment", environment.value.name);
  formData.append("theme", theme.value.code);

  if (null !== dateFrom.value) {
    formData.append("dateFrom", dateFrom.value);
  }

  if (null !== dateTo.value) {
    formData.append("dateTo", dateTo.value);
  }

  if (null !== familyId.value) {
    formData.append("familyId", familyId.value);
  }

  findpurgedObjectsToExport(formData);
};

function resetBasicSearchValues() {
  environment.value = null;
  theme.value = null;

  advancedSearchDisplayed.value = false;
}

function resetAdvancedSearchValues() {
  dateFrom.value = null;
  dateTo.value = null;
  familyId.value = null;
  advancedSearchDone.value = false;
}

/**
 * Resets the current page and the DataTable component state.
 *
 * It is not reset by default when the bound value changes after a form submission.
 */
function resetPaginationAndSort(): void {
  // First page displayed (1st row means 1st page).
  purgedObjectsTable.value.d_first = 0;
  // The page stored after the last DataTablePageEvent must be reset, too.
  paginationPage.value = 0;

  // Sorting criteria.
  purgedObjectsTable.value.d_sortField = undefined;
  purgedObjectsTable.value.d_sortOrder = 0;
}
</script>

<style scoped></style>
