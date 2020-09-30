<template>
  <q-page padding>
    <div class="row">
      <div class="col-12">
        <q-input v-model="idFamille" label="ID famille" v-on:blur="initByDate()" />
      </div>
    </div>
    <div class="row">
      <div class="col-sm-auto col-xs-12">
        <q-date
          v-model="date"
          :events="events"
          v-on:navigation="navigation"
          event-color="orange"
          :title="jourMois"
        />
      </div>
      <div class="col-sm col-xs-12 q-ml-md">
        <div class="text-h4 q-mb-md">Mes gardes sur {{nomMois}}</div>
        <ul>
          <li v-for="gardeDetail in gardesDetails">
            {{gardeDetail.jour}} de {{gardeDetail.heureArrivee}} à {{gardeDetail.heureDepart}}<span v-if="gardeDetail.commentaire"> ({{gardeDetail.commentaire}})</span>
          </li>
        </ul>
      </div>
    </div>
  </q-page>
</template>

<script lang="ts">
import Vue from 'vue'
import Component from 'vue-class-component'
import { Garde } from 'src/interfaces/garde'
import { date } from 'quasar'
import { gardeService } from 'src/services/garde.service'
import { dateUtils } from 'src/utils/date.utils';

@Component
export default class MesGardes extends Vue {
  idFamille = '1' // TODO à supprimer
  date?: string
  nomMois?: string
  jourMois?: string
  premierLundiDuMois?: Date
  dernierVendrediDuMois?: Date
  gardes: Garde[] = []
  events: string[] = []
  gardesDetails: {jour: string, heureArrivee: string, heureDepart: string, commentaire: string}[] = []

  async created() {
    await this.initByDate(new Date())
  }

  async initByDate(initDate?: Date) {
    if (initDate === undefined) { // TODO à supprimer
      initDate = this.date ? dateUtils.qDateToDate(this.date) : new Date()
    }
    this.date = dateUtils.dateToQDate(initDate)
    this.jourMois = dateUtils.dateToJourComplet(initDate)
    this.nomMois = date.formatDate(initDate, 'MMMM')

    this.premierLundiDuMois = date.startOfDate(initDate, 'month')
    // Recherche du premier lundi
    while (this.premierLundiDuMois.getDay() !== 1) {
      this.premierLundiDuMois = date.addToDate(this.premierLundiDuMois, { days: 1 })
    }
    // Recherche du dernier vendredi de la semaine du dernier lundi
    // On recherche d'abord le dernier lundi
    this.dernierVendrediDuMois = date.endOfDate(initDate, 'month')
    while (this.dernierVendrediDuMois.getDay() !== 1) {
      this.dernierVendrediDuMois = date.subtractFromDate(this.dernierVendrediDuMois, { days: 1 })
    }
    // Puis on recherche le vendredi de cette semaine
    while (this.dernierVendrediDuMois.getDay() !== 5) {
      this.dernierVendrediDuMois = date.addToDate(this.dernierVendrediDuMois, { days: 1 })
    }
    // On récupère les gardes entre ces 2 dates pour cette famille
    this.gardes = await gardeService.findByFamilleIriAndJourPlanningDateBetween(`/api/familles/${this.idFamille}`, this.premierLundiDuMois, this.dernierVendrediDuMois)
    // On transforme les dates des jours planning pour l'affichage des évènements dans le QDate
    this.events = this.gardes.map(garde => dateUtils.dateToQDate(garde.jourPlanning?.date))
    // On transforme ces mêmes dates pour afficher correctement les gardes dans la liste
    this.gardesDetails = this.gardes.map(garde => ({
      jour: dateUtils.dateToJourComplet(garde.jourPlanning?.date),
      heureArrivee: dateUtils.dateToHeure(garde.heureArrivee),
      heureDepart: dateUtils.dateToHeure(garde.heureDepart),
      commentaire: garde.commentaire
    }))
  }

  // input(value: string, reason: string, details: any) {
  //   console.log({value, reason, details})
  // }

  async navigation(view: {year: number, month: number}) {
    await this.initByDate(date.buildDate(view))
  }

}
</script>
