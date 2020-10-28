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
        <div class="text-h4 q-mb-md">Mes dispos sur {{nomMois}}</div>
        <ul>
          <li v-for="gardeDetail in gardesDetails">
            <q-checkbox v-model="gardeDetail.familleDisponible">
              {{gardeDetail.jour}} de {{gardeDetail.heureArrivee}} à {{gardeDetail.heureDepart}}<span v-if="gardeDetail.commentaire"> ({{gardeDetail.commentaire}})</span>
            </q-checkbox>
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
import { mesDisposDuMoisService } from 'src/services/mes-dispos-du-mois.service';
import { MesDisposDuMois } from 'src/interfaces/mesdisposdumois';

@Component
export default class MesGardes extends Vue {
  date?: string
  dateObj?: Date
  nomMois?: string
  jourMois = ''
  annee?: number
  familleId?: string
  mesDisposDuMois: MesDisposDuMois = null
  events: string[] = []
  gardesDetails: {jour: string, heureArrivee: string, heureDepart: string, commentaire?: string, familleDisponible: boolean}[] = []

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
    const familleId = familleStore.state.familleSelectionnee?.id

    if (nomMois !== this.nomMois || annee !== this.annee || familleId !== this.familleId) {
      this.nomMois = nomMois
      this.annee = annee
      this.familleId = familleId

      const numMois = date.formatDate(initDate, 'MM');

      // On récupère les dispos du mois
      this.mesDisposDuMois = await mesDisposDuMoisService.findOneByCodeMoisPlanningAndIdFamille(`${annee}-${numMois}`, familleId)
      // On transforme les dates des jours planning pour l'affichage des évènements dans le QDate
      this.events = this.mesDisposDuMois.gardesDisponibles.map(gardeDisponible => dateUtils.dateToQDate(gardeDisponible.garde.jourPlanning.date))
      // On transforme ces mêmes dates pour afficher correctement les gardes dans la liste
      this.gardesDetails = this.mesDisposDuMois.gardesDisponibles.map(gardeDisponible => ({
        jour: dateUtils.dateToJourComplet(gardeDisponible.garde.jourPlanning.date),
        heureArrivee: dateUtils.dateToHeure(gardeDisponible.garde.heureArrivee),
        heureDepart: dateUtils.dateToHeure(gardeDisponible.garde.heureDepart),
        commentaire: gardeDisponible.garde.commentaire,
        familleDisponible: gardeDisponible.familleDisponible
      }))
    }
  }

  async input(value: string, reason: string, details: any) {
    await this.initByDate(dateUtils.qDateToDate(value))
  }

  async navigation(view: {year: number, month: number}) {
    await this.initByDate(date.buildDate(view) as unknown as Date) // TODO supprimer le cast dès que https://github.com/quasarframework/quasar/pull/7888 est rentré dans la version courante
  }

  @Watch('familleStore.state.familleSelectionnee')
  async familleSelectionnee() {
    await this.initByDate(this.dateObj || new Date())
  }

}
</script>
