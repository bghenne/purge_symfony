<script setup lang="ts">

import {EligibleObject} from "../types/eligible-object";
import {Button, Column, DataTable, Message, Select, Toast} from "primevue";
import {Form} from "@primevue/forms";
import {onMounted, ref} from 'vue';
import {zodResolver} from '@primevue/forms/resolvers/zod';
import {useToast} from "primevue/usetoast";
import {z} from 'zod';
import {fetchEnvironments} from "../composables/environment";
import {fetchThemes} from "../composables/theme";
import {ObjectType} from "../enums/object-type";
import {doRequest} from "../utilities/request";
import {Methods} from "../enums/methods";

const eligibleObjects = ref([] as EligibleObject[]);
const environments = ref({});
const themes = ref({});

environments.value = fetchEnvironments();
themes.value = fetchThemes(ObjectType.ELIGIBLE);

const toast = useToast();

const resolver = ref(zodResolver(
    z.object({
      environment: z.union([
        z.object({
          name: z.string().min(1, 'Environnement requis.')
        }),
        z.any().refine((val) => false, { message: 'Environnement requis.' })
      ]),
      theme: z.union([
        z.object({
          code: z.string().min(1, 'Thème requis.')
        }),
        z.any().refine((val) => false, { message: 'Thème requis.' })
      ])
    })
));

const onFormSubmit = (event) => {
  if (event.valid) {
    toast.add({severity: 'success', summary: 'Form is submitted.', life: 3000});

    const formData = new FormData;
    formData.append('environment', event.values.environment.name);
    formData.append('theme', event.values.theme.code);

    doRequest('/api/eligible-object', Methods.POST, formData)
        .then((newEligibleObjects : EligibleObject[]) => {
          eligibleObjects.value = newEligibleObjects;
        })
        .catch(error => console.log(error))
  }
};

</script>

<template>

  <div class="card flex justify-center mb-4">
    <Toast/>
    <Form v-slot="$form" :resolver="resolver" @submit="onFormSubmit">
      <div class="flex gap-5">
        <Select name="environment" :options="environments" optionLabel="name" placeholder="Choisissez un environnement" fluid checkmark/>
        <Message v-if="$form.environment?.invalid" severity="error" size="small" variant="simple">{{
            $form.environment.error.message
          }}
        </Message>
        <Select name="theme" :options="themes" optionLabel="name" placeholder="Choisissez un thème" fluid checkmark/>
        <Message v-if="$form.theme?.invalid" severity="error" size="small" variant="simple">{{
            $form.theme.error.message
          }}
        </Message>
        <Button type="submit" severity="secondary" label="Submit" class="shrink-0" />
      </div>
    </Form>
  </div>

  <DataTable
      v-if="eligibleObjects.length > 0"
      v-model:expandedRows="eligibleObjects.details"
      dataKey="key"
      :value="eligibleObjects"
      removableSort
      paginator
      :rows="5"
  >
    <Column expander style="width: 5rem"/>
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

<style scoped></style>