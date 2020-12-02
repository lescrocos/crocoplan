<template>
  <q-page padding>
    <div class="row">
      <div class="col-all">
        <q-date
          ref="calendrier"
          v-model="currentRange"
          @range-start="rangeStart"
          @input="input"
          event-color="orange"
          :title="titreCalendrier"
          today-btn
          range
        />
      </div>
      <div class="col-all">
        <div class="text-h4 q-mb-md">Planning sur la {{titreCalendrier}}</div>
        <div class="row">
          <div class="col-all" v-for="jourPlanningAffichable in joursPlanningAffichables">
            <q-table
              :title="jourPlanningAffichable.jourPlanning.date"
              :data="jourPlanningAffichable.lignes"
              :columns="[{field: 'nom', label: 'Qui ?'}, {field: 'horaires', label: 'Horaires'}]"
              :rows-per-page-options="[0]"
              :hide-pagination="true"
              separator="cell"
            >
              <template v-slot:body="props">
                <q-tr v-show="props.row.titre" :props="props">
                  <q-td colspan="100%">
                    <div class="text-left">{{ props.row.titre }}</div>
                  </q-td>
                </q-tr>
                <q-tr :props="props">
                  <q-td auto-width>
                    {{ props.row.nom }}
                  </q-td>
                  <q-td v-if="props.row.present">
                    <q-range
                      class="horaire"
                      v-model="props.row.horairesRange"
                      :min="8 * 60"
                      :max="19 * 60"
                      :step="15"
                      :left-label-value="horaireRangeValueToLabel(props.row.horairesRange.min)"
                      :right-label-value="horaireRangeValueToLabel(props.row.horairesRange.max)"
                      :readonly="true"
                      markers
                      label-always
                      drag-range
                      snap
                    />
                  </q-td>
                  <q-td v-if="!props.row.present">
                    {{ props.row.absenceType }}
                  </q-td>
                </q-tr>
              </template>
            </q-table>
          </div>
        </div>
      </div>
    </div>
  </q-page>
</template>

<style lang="stylus">
.horaire
  .q-slider__track--h
    margin-top: -5px
    height: 10px
</style>

<script lang="ts">
import Vue from 'vue';
import Component from 'vue-class-component';
import { date } from 'quasar';
import { dateUtils } from 'src/utils/date.utils';
import { jourPlanningService } from 'src/services/jour-planning.service';
import { JourPlanning } from 'src/interfaces/jourplanning';
import { proStore } from 'src/store/pro.store';

interface JourPlanningAffichableLigne {
  nom: string;
  present: boolean;
  heureArrivee?: string;
  heureDepart?: string;
  horairesRange: {min?: number, max?: number}
  absenceType?: string;
  titre?: string;
}

@Component
export default class Planning extends Vue {
  currentDate?: Date;
  currentRange: { from?: string, to?: string } = {};
  titreCalendrier = ''
  joursPlanning: JourPlanning[] = []
  joursPlanningAffichables: {jourPlanning: JourPlanning, lignes: JourPlanningAffichableLigne[]}[] = []

  async mounted() {
    await this.initByDate(new Date())
  }

  async navigation(view: {year: number, month: number}) {
    console.log({view})
  }

  async input(value: string, reason: string, details: {year: number, month: number, day: number}) {
    const selectedDate = date.buildDate({year: details.year, month: details.month, date: details.day})
    await this.initByDate(selectedDate)
  }

  async rangeStart(from: {year: number, month: number, day: number}) {
    const selectedDate = date.buildDate({year: from.year, month: from.month, date: from.day})
    await this.initByDate(selectedDate);
  }

  private async initByDate(selectedDate: Date) {
    this.currentDate = selectedDate
    const dayOfWeek = date.getDayOfWeek(selectedDate); // Lundi = 1, Dimanche = 7
    const startOfWeekDate = date.subtractFromDate(selectedDate, {days: dayOfWeek - 1}); // Lundi : on retire 0 jours, Mardi : on retire 1 jours, ...
    const endOfWeekDate = date.addToDate(date.clone(startOfWeekDate), {days: 6});
    this.currentRange = {from: dateUtils.dateToQDate(startOfWeekDate), to: dateUtils.dateToQDate(endOfWeekDate)};
    // On modifie l'"editRange" interne au composant QDate pour lui donner la valeur void 0 (c'est à dire undefined https://stackoverflow.com/a/7452352/535203 ) et lui faire croire qu'il n'y a plus aucune sélection en cours. Cf https://github.com/quasarframework/quasar/blob/5c7b5f298d8216d51646b461653e49493daaa599/ui/src/components/date/QDate.js#L1205
    this.$refs.calendrier.$data.editRange = void 0;
    this.titreCalendrier = `Semaine du ${startOfWeekDate.getDate()} ${date.formatDate(startOfWeekDate, 'MMMM')}`
    this.joursPlanning = await jourPlanningService.findAllByDateBetween(startOfWeekDate, endOfWeekDate)
    const prosById = await proStore.getProsById()
    this.joursPlanningAffichables = this.joursPlanning.map(jourPlanning => {
      const jourPlanningAffichableLignes: JourPlanningAffichableLigne[] = []
      jourPlanning.presencesPros?.forEach((presencePro, index) => {
        jourPlanningAffichableLignes.push({
          nom: prosById.get(presencePro.pro.id).nom,
          present: presencePro.present,
          heureArrivee: presencePro.heureArrivee,
          heureDepart: presencePro.heureDepart,
          horairesRange: {
            min: this.dateStrToHoraireRangeValue(presencePro.heureArrivee),
            max: this.dateStrToHoraireRangeValue(presencePro.heureDepart)
          },
          absenceType: presencePro.absenceType,
          titre: index == 0 ? 'Horaires pros' : undefined
        })
      })
      return {
        jourPlanning,
        lignes: jourPlanningAffichableLignes
      }
    })
  }

  horaireRangeValueToLabel(horaireRange?: number): string | undefined {
    return horaireRange ? parseInt(horaireRange / 60) + 'h' + (horaireRange % 60 || '00') : undefined
  }

  dateStrToHoraireRangeValue(dateStr?: string): number | undefined {
    const date = dateStr ? new Date(dateStr) : undefined
    return date ? date.getHours() * 60 + date.getMinutes() : undefined
  }

}
</script>
