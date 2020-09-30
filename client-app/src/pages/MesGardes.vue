<template>
  <q-page padding>
    <div class="row">
      <div class="col-sm-auto col-xs-12">
        <q-date
          :value="date"
          v-on:input="input"
          :events="events"
          v-on:navigation="navigation"
          event-color="orange"
        />
      </div>
      <div class="col-sm col-xs-12 q-ml-md">
        <div class="text-h4 q-mb-md">Gardes du mois</div>
        <ul>
          <li v-for="garde in gardes">
            {{garde.jourPlanning.date}} {{garde.heureArrivee}}-{{garde.heureDepart}} ({{garde.commentaire}})
          </li>
        </ul>
      </div>
    </div>
  </q-page>
</template>

<script lang="ts">
import Vue from 'vue'
import Component from 'vue-class-component'
import {Garde} from "../interfaces/garde"
import { date } from 'quasar'
import { gardeService } from 'src/services/garde.service';

@Component()
export default class MesGardes extends Vue {
  date: string;
  premierLundi: Date;
  dernierVendredi: Date;
  gardes: Garde[] = [];
  events: string[] = [];

  created() {
    const currentDate = new Date();
    this.date = date.formatDate(currentDate, 'YYYY/MM/DD');

    this.premierLundi = date.startOfDate(currentDate, 'month');
    // Recherche du premier lundi
    while (this.premierLundi.getDay() !== 1) {
      this.premierLundi = date.addToDate(this.premierLundi, { days: 1 });
    }
    // Recherche du dernier vendredi de la semaine du dernier lundi
    // On recherche d'abord le dernier lundi
    this.dernierVendredi = date.endOfDate(currentDate, 'month');
    while (this.dernierVendredi.getDay() !== 1) {
      this.dernierVendredi = date.subtractFromDate(this.dernierVendredi, { days: 1 });
    }
    // Puis on recherche le vendredi de cette semaine
    while (this.dernierVendredi.getDay() !== 5) {
      this.dernierVendredi = date.addToDate(this.dernierVendredi, { days: 1 });
    }
    gardeService.findByFamilleIriAndJourPlanningDateBetween('/api/familles/201', this.premierLundi, this.dernierVendredi)
      .then(gardes => {
        this.gardes = gardes
      });
  }

  input(value: string, reason: string, details: any) {
    console.log({value, reason, details});
  }

  navigation(view: {year: number, month: number}) {
    console.log({view});
  }
}
</script>
