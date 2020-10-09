<template>
  <q-page padding>
    <div class="row">
      <div class="col-12">
        <q-select filled v-model="idFamille" :options="familles" label="Famille" option-value="id" option-label="nom" v-on:input="initByDate()" emit-value map-options />
      </div>
    </div>
    <div class="row">
      <div class="col-sm-auto col-xs-12">
        <q-date
          v-model="date"
          @input="input"
          :events="events"
          @navigation="navigation"
          event-color="orange"
          :title="jourMois"
          today-btn
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
import { familleService } from 'src/services/famille.service';
import { Famille } from 'src/interfaces/famille';

@Component
export default class MesGardes extends Vue {
  idFamille = '' // TODO à supprimer
  familles: Famille[] = [] // TODO à supprimer
  date?: string
  nomMois?: string
  jourMois = ''
  annee?: number
  gardes: Garde[] = []
  events: string[] = []
  gardesDetails: {jour: string, heureArrivee: string, heureDepart: string, commentaire?: string}[] = []

  async created() {
    familleService.findAll().then(familles => this.familles = familles)
    await this.initByDate(new Date())
  }

  async initByDate(initDate?: Date) {
    if (initDate === undefined) { // TODO à supprimer
      initDate = this.date ? dateUtils.qDateToDate(this.date) : new Date()
    }
    this.date = dateUtils.dateToQDate(initDate)
    this.jourMois = dateUtils.dateToJourComplet(initDate);
    const nomMois = date.formatDate(initDate, 'MMMM')
    const annee = initDate.getFullYear();

    const premierJourDuMois = date.startOfDate(initDate, 'month')
    const dernierJourDuMois = date.endOfDate(initDate, 'month')

    if (nomMois !== this.nomMois || annee !== this.annee) {
      this.nomMois = nomMois
      this.annee = annee
      // On récupère les gardes entre ces 2 dates pour cette famille
      this.gardes = await gardeService.findAllByFamilleIriAndJourPlanningDateBetween(`/api/familles/${this.idFamille}`, premierJourDuMois, dernierJourDuMois)
      // On transforme les dates des jours planning pour l'affichage des évènements dans le QDate
      this.events = this.gardes.map(garde => dateUtils.dateToQDate(garde.jourPlanning.date))
      // On transforme ces mêmes dates pour afficher correctement les gardes dans la liste
      this.gardesDetails = this.gardes.map(garde => ({
        jour: dateUtils.dateToJourComplet(garde.jourPlanning.date),
        heureArrivee: dateUtils.dateToHeure(garde.heureArrivee),
        heureDepart: dateUtils.dateToHeure(garde.heureDepart),
        commentaire: garde.commentaire
      }))
    }
  }

  async input(value: string, reason: string, details: any) {
    await this.initByDate(dateUtils.qDateToDate(value))
  }

  async navigation(view: {year: number, month: number}) {

    await this.initByDate(date.buildDate(view) as Date)
  }

}
</script>
