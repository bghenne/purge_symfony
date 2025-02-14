<template>
  <Section>
    <Heading>
      Recherche avancée
    </Heading>
    <Form v-slot="$form" :resolver="resolver" @submit="onFormSubmit">
      <DatePicker v-model="dateFrom" dateFormat="dd/mm/yy" fluid/>
      <DatePicker v-model="dateTo" dateFormat="dd/mm/yy" fluid/>
      <InputText name="familyId" type="text" placeholder="IDFASS"/>
      <div v-if="'/control-alert' === $route.path">
        <InputText name="familyId" type="text" placeholder="ID Prestation"/>
        <Select name="typology" :options="typologies" placeholder="Typlogie de prestation" fluid checkmark/>
      </div>
      <Button type="reset" label="Effacer" class="shrink-0" />
      <Button type="submit" severity="secondary" label="Valider" class="shrink-0" />
    </Form>
  </Section>
</template>

<script setup lang="ts">
import DatePicker from 'primevue/datepicker';
import {Button, InputText, Select} from "primevue";
import Heading from "./Heading.vue";
import Section from "./Section.vue";
import {zodResolver} from '@primevue/forms/resolvers/zod';
import {z} from 'zod';
import {Form, FormSubmitEvent} from "@primevue/forms";
import {ref} from "vue";

const typologies = [
    'Audio',
    'Vidéo',
    'Autres'
];

const resolver = ref(zodResolver(
    z.object({
      dateFrom: z.preprocess((val) => {
        if (val === '' || val === null) {
          return null;
        }

        return new Date(val);
      }, z.union([z.date(), z.null().refine((val) => val !== null, {message: 'Date is required.'})])),
      dateTo: z.preprocess((val) => {
        if (val === '' || val === null) {
          return null;
        }

        return new Date(val);
      }, z.union([z.date(), z.null().refine((val) => val !== null, {message: 'Date is required.'})]))
    })
));

const onFormSubmit = (event: FormSubmitEvent) => {
  console.log(event)
}


</script>

<style scoped>
</style>
