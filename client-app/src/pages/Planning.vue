<template>
  <q-page padding>
    <div class="row">
      <div class="col-all">
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
        <div class="text-h4 q-mb-md">Planning sur {{nomMois}}</div>
        <ul>
          <li v-for="gardeDisponible in gardesDisponibles">
            <q-checkbox v-model="gardeDisponible.familleDisponible">
              {{gardeDisponible.gardeAffichable.jour}} de {{gardeDisponible.gardeAffichable.heureArrivee}} à {{gardeDisponible.gardeAffichable.heureDepart}}<span v-if="gardeDisponible.garde.commentaire"> ({{gardeDisponible.garde.commentaire}})</span>
            </q-checkbox>
          </li>
        </ul>
        <q-btn @click="sauvegarder">
          Sauvegarder
        </q-btn>
      </div>
    </div>
  </q-page>
</template>

<script lang="ts">
import Vue from 'vue';
import Component from 'vue-class-component';
import { Garde } from 'src/interfaces/garde';
import { date } from 'quasar';
import { dateUtils } from 'src/utils/date.utils';
import { familleStore } from 'src/store/famille.store';
import { Watch } from 'vue-property-decorator';
import { mesDisposDuMoisService } from 'src/services/mes-dispos-du-mois.service';
import { MesDisposDuMois } from 'src/interfaces/mesdisposdumois';
import { GardeAffichable } from 'src/interfaces/garde-affichable';
import { gardeUtils } from 'src/utils/garde.utils';

interface GardeDisponible {
  gardeAffichable: GardeAffichable;
  garde: Garde;
  familleDisponible?: boolean;
}

@Component
export default class Planning extends Vue {
  date?: string
  dateObj?: Date
  nomMois?: string
  jourMois = ''
  annee?: number
  familleId?: string
  mesDisposDuMois: MesDisposDuMois = {gardes: []}
  events: string[] = []
  gardesDisponibles: GardeDisponible[] = []
  gardesDisponiblesByJourPlanningQDate: Map<string, GardeDisponible[]> = new Map<string, GardeDisponible[]>()

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

      const numMois = date.formatDate(initDate, 'MM')

      if (familleId !== undefined) {
        // On récupère les dispos du mois
        const mesDisposDuMois = await mesDisposDuMoisService.findOneByCodeMoisPlanningAndIdFamille(`${annee}-${numMois}`, familleId)
        this.initMesDisposDuMois(mesDisposDuMois)
      }
    }
  }

  private initMesDisposDuMois(mesDisposDuMois: MesDisposDuMois) {
    this.mesDisposDuMois = mesDisposDuMois

    // On garde dans un coin les gardes affichables
    const gardesDisponiblesIdsSet = new Set(mesDisposDuMois.gardesDisponiblesIds)
    this.gardesDisponibles = this.mesDisposDuMois.gardes.map(garde => ({
      garde,
      gardeAffichable: gardeUtils.gardeToGardeAffichable(garde),
      familleDisponible: gardesDisponiblesIdsSet.has(garde.id)
    }))

    // On les regroupe par jour pour pouvoir plus tard savoir si on a un événement de coché pour ce jour
    this.gardesDisponiblesByJourPlanningQDate = new Map<string, GardeDisponible[]>()
    this.gardesDisponibles.forEach(gardeDisponible => {
      const jourPlanningQDate = dateUtils.dateToQDate(gardeDisponible.garde.jourPlanning.date)
      const gardesDisponibles = this.gardesDisponiblesByJourPlanningQDate.get(jourPlanningQDate) || []
      gardesDisponibles.push(gardeDisponible)
      this.gardesDisponiblesByJourPlanningQDate.set(jourPlanningQDate, gardesDisponibles)
    })

    // On transforme les jour où la famille est dispo en événement à afficher dans le calendrier
    this.events = []
    this.gardesDisponiblesByJourPlanningQDate.forEach((gardesDisponibles, jourPlanningQDate) => {
      for (const gardeDisponible of gardesDisponibles) {
        if (gardeDisponible.familleDisponible) {
          this.events.push(jourPlanningQDate)
          break
        }
      }
    })
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

  async sauvegarder() {
    const gardesDisponiblesIds = (this.gardesDisponibles)
      .filter(gardeDisponible => gardeDisponible.familleDisponible)
      .map(gardeDisponible => gardeDisponible.garde.id) as string[]
    const mesDisposDuMois = await mesDisposDuMoisService.update({
      code: this.mesDisposDuMois.code,
      gardesDisponiblesIds
    })
    this.initMesDisposDuMois(mesDisposDuMois)
  }

}
</script>
