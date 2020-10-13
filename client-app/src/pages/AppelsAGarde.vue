<template>
  <q-page padding>
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
        <div class="text-h4 q-mb-md">Appels à garde sur {{nomMois}}</div>
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
import { dateUtils } from 'src/utils/date.utils'
import { familleStore } from 'src/store/famille.store'
import { Watch } from 'vue-property-decorator'

@Component
export default class AppelsAGarde extends Vue {
  date?: string
  dateObj?: Date
  nomMois?: string
  jourMois = ''
  annee?: number
  gardes: Garde[] = []
  events: string[] = []
  gardesDetails: {jour: string, heureArrivee: string, heureDepart: string, commentaire?: string}[] = []

  familleStore = familleStore

  async created() {
    await this.initByDate(new Date())
  }

  async initByDate(initDate: Date) {
    this.date = dateUtils.dateToQDate(initDate)
    this.dateObj = initDate
    this.jourMois = dateUtils.dateToJourComplet(initDate)
    const nomMois = date.formatDate(initDate, 'MMMM')
    const annee = initDate.getFullYear()

    if (nomMois !== this.nomMois || annee !== this.annee) {
      this.nomMois = nomMois
      this.annee = annee

      const premierJourDuMois = date.startOfDate(initDate, 'month')
      const dernierJourDuMois = date.endOfDate(initDate, 'month')

      // On récupère les gardes entre ces 2 dates pour cette famille
      this.gardes = await gardeService.findAllByFamilleIriAndJourPlanningDateBetween(null, premierJourDuMois, dernierJourDuMois)
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
    await this.initByDate(date.buildDate(view) as unknown as Date) // TODO supprimer le cast dès que https://github.com/quasarframework/quasar/pull/7888 est rentré dans la version courante
  }

}
</script>
