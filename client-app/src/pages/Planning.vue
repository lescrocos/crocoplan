<template>
  <q-page padding>
    <div class="row q-gutter-md">
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
      </div>
      <q-card v-for="jourPlanningAffichable in joursPlanningAffichables" class="col-all">
        <q-card-section class="">
          <div class="text-h5 q-mb-md">{{ jourPlanningAffichable.jourDate }}</div>
        </q-card-section>
        <q-separator inset="true" />
        <q-card-section horizontal class="row">
          <q-markup-table class="table-horaire col-9">
            <thead>
              <tr>
                <th>
                  Qui ?
                </th>
                <th>
                  Commentaire
                </th>
                <th v-if="!edit" v-for="titre in horaireTitreColumns" class="titre-horaire-cell" colspan="4">
                  {{ titre }}
                </th>
                <th v-if="edit">
                  Horaires
                </th>
              </tr>
            </thead>
            <tbody>
              <template v-for="ligne in jourPlanningAffichable.lignes">
                <tr v-if="ligne.titre" class="titre-ligne-horaire">
                  <td colspan="100%">
                    <div class="text-left">{{ ligne.titre }}</div>
                  </td>
                </tr>
                <tr class="ligne-horaire">
                  <td>
                    {{ ligne.nom }}&nbsp;
                  </td>
                  <td>
                    {{ ligne.commentaire }}
                  </td>
                  <td v-if="ligne.present && !edit" v-for="horaireColumn in horaireColumns" :class="computeCellHoraireClass(ligne, horaireColumn)">
                    <div v-if="Math.abs(horaireColumn.minRangeValue - ligne.horairesRange.min) < stepMinutes" class="cell-horaire-display-container">
                      <div class="cell-horaire-display">{{ horaireRangeValueToLabel(ligne.horairesRange.min) }}</div>
                    </div>
                    <div v-if="Math.abs(horaireColumn.minRangeValue - ligne.horairesRange.max + 3 * stepMinutes) < stepMinutes" class="cell-horaire-display-container">
                      <div class="cell-horaire-display">{{ horaireRangeValueToLabel(ligne.horairesRange.max) }}</div>
                    </div>
                  </td>
                  <td v-if="ligne.present && edit">
                    <q-range
                      class="horaire"
                      v-model="ligne.horairesRange"
                      :min="heureDebut * 60"
                      :max="heureFin * 60"
                      :step="stepMinutes"
                      :left-label-value="horaireRangeValueToLabel(ligne.horairesRange.min)"
                      :right-label-value="horaireRangeValueToLabel(ligne.horairesRange.max)"
                      :readonly="true"
                      markers
                      label-always
                      drag-range
                      snap
                    />
                  </td>
                  <td v-if="!ligne.present" colspan="100%">
                    {{ ligne.absenceType }}
                  </td>
                </tr>
              </template>
            </tbody>
          </q-markup-table>
          <q-card-section class="col-3">
            <q-card-section>
              <div class="text-h6 q-mb-md">Notes</div>
              <div class="text-subtitle1">{{ jourPlanningAffichable.jourPlanning.commentaire }}</div>
            </q-card-section>
            <q-card-section>
              <div class="text-h6 q-mb-md">Présences ({{ jourPlanningAffichable.presences.enfants.size }})</div>
              <ul>
                <li v-for="nomGroupe in Object.keys(jourPlanningAffichable.presences.parGroupes)">
                  <span class="text-bold">{{ nomGroupe }} ({{ jourPlanningAffichable.presences.parGroupes[nomGroupe].size }})</span> : {{ Array.from(jourPlanningAffichable.presences.parGroupes[nomGroupe]).map(enfant => enfant.nom).sort().join(', ') }}
                </li>
              </ul>
            </q-card-section>
            <q-card-section>
              <div class="text-h6 q-mb-md">Absences ({{ jourPlanningAffichable.absences.enfants.size }})</div>
              <ul>
                <li v-for="nomGroupe in Object.keys(jourPlanningAffichable.absences.parGroupes)">
                  <span class="text-bold">{{ nomGroupe }} ({{ jourPlanningAffichable.absences.parGroupes[nomGroupe].size }})</span> : {{ Array.from(jourPlanningAffichable.absences.parGroupes[nomGroupe]).map(enfant => enfant.nom).join(', ') }}
                </li>
              </ul>
            </q-card-section>
          </q-card-section>
        </q-card-section>
      </q-card>
    </div>
  </q-page>
</template>

<style lang="stylus">
.horaire
  .q-slider__track--h
    margin-top: -5px
    height: 10px

.ligne-horaire
  .debut-heure
    border-left 1px solid green
    border-right none

  .debut-demie-heure
    border-left 1px dashed green
    border-right none

  .debut-quart-heure
    border-left none

  .fin-heure
    border-left none
    border-right 1px solid green

  .personne-presente
    background-color lightgreen

  .cell-horaire
    padding-left 5px
    padding-right 5px

  .cell-horaire-display-container
    position relative

  .cell-horaire-display
    position absolute
    z-index 1
    font-weight bold

.titre-horaire-cell
  text-align left

.titre-ligne-horaire
  background-color $primary

.table-horaire table
  thead,tbody,td,tr
    line-height normal
    height 24px

</style>

<script lang="ts">
import Vue from 'vue';
import Component from 'vue-class-component';
import { date } from 'quasar';
import { dateUtils } from 'src/utils/date.utils';
import { jourPlanningService } from 'src/services/jour-planning.service';
import { JourPlanning } from 'src/interfaces/jourplanning';
import { proStore } from 'src/store/pro.store';
import { familleStore } from 'src/store/famille.store';
import { Enfant } from 'src/interfaces/enfant';
import { enfantStore } from 'src/store/enfant.store';

interface JourPlanningAffichableLigne {
  nom?: string
  present: boolean
  heureArrivee?: string
  heureDepart?: string
  horairesRange: {min?: number, max?: number}
  absenceType?: string
  commentaire?: string
  titre?: string
}

interface HoraireColumn {
  minRangeValue: number
  maxRangeValue: number
}

class PresencesAbsences {
  parGroupes: {[nomGroupe: string]: Set<Enfant>} = {}
  enfants: Set<Enfant> = new Set<Enfant>()
}

@Component
export default class Planning extends Vue {
  currentDate?: Date
  currentRange: { from?: string, to?: string } = {}
  titreCalendrier = ''
  joursPlanning: JourPlanning[] = []
  joursPlanningAffichables: {jourPlanning: JourPlanning, lignes: JourPlanningAffichableLigne[], jourDate: string, presences: PresencesAbsences, absences: PresencesAbsences}[] = []
  edit = false
  heureDebut = 8
  heureFin = 19
  stepMinutes = 15
  horaireColumns: HoraireColumn[] = []
  horaireTitreColumns: (string|undefined)[] = []

  constructor() {
    super()
    // Création des colonnes de la partie horaire d'une ligne
    for (let i = this.heureDebut * 60; i < this.heureFin * 60; i += this.stepMinutes) {
      this.horaireColumns.push({minRangeValue: i, maxRangeValue: i + this.stepMinutes})
    }
    // Initialisation des colonnes d'un jour
    this.horaireColumns.forEach(horaireColumn => {
      if (horaireColumn.minRangeValue % 60 === 0) {
        this.horaireTitreColumns.push(this.horaireRangeValueToLabel(horaireColumn.minRangeValue))
      }
    })
  }

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
    const prosById = await proStore.getAllById()
    for (const jourPlanning of this.joursPlanning) {
      const jourPlanningAffichableLignes: JourPlanningAffichableLigne[] = []
      jourPlanning.presencesPros?.forEach((presencePro, index) => {
        jourPlanningAffichableLignes.push({
          nom: prosById.get(presencePro.pro.id)?.nom,
          present: presencePro.present,
          heureArrivee: presencePro.heureArrivee,
          heureDepart: presencePro.heureDepart,
          horairesRange: {
            min: this.dateStrToHoraireRangeValue(presencePro.heureArrivee),
            max: this.dateStrToHoraireRangeValue(presencePro.heureDepart)
          },
          absenceType: presencePro.absenceType,
          commentaire: presencePro.commentaire,
          titre: index == 0 ? 'Horaires pros' : undefined
        })
      })

      // Ajout des gardes
      const gardesLignes: JourPlanningAffichableLigne[] = []
      const appelsAGardesLignes: JourPlanningAffichableLigne[] = []
      for (const garde of jourPlanning.gardes) {
        const ligne = {
          present: true,
          heureArrivee: garde.heureArrivee,
          heureDepart: garde.heureDepart,
          horairesRange: {
            min: this.dateStrToHoraireRangeValue(garde.heureArrivee),
            max: this.dateStrToHoraireRangeValue(garde.heureDepart)
          },
          commentaire: garde.commentaire
        }
        if (garde.famille) {
          // La garde est pourvue
          gardesLignes.push({
            nom: (await familleStore.getFamilleById(garde.famille.id))?.nom,
            titre: gardesLignes.length == 0 ? 'Gardes' : undefined,
            ...ligne
          })
        } else {
          // La garde est à pourvoir
          appelsAGardesLignes.push({
            nom: '',
            titre: appelsAGardesLignes.length == 0 ? 'Appels à garde' : undefined,
            ...ligne
          })
        }
      }
      jourPlanningAffichableLignes.push(...gardesLignes)
      jourPlanningAffichableLignes.push(...appelsAGardesLignes)

      // Ajout des présences et absences
      const presences = new PresencesAbsences()
      const absences = new PresencesAbsences()

      if (jourPlanning.presencesEnfants) {
        for (const presenceEnfant of jourPlanning.presencesEnfants) {
          const presenceAbsence = presenceEnfant.present ? presences : absences
          const enfant = await enfantStore.getById(presenceEnfant.enfant.id as string) as Enfant
          presenceAbsence.enfants.add(enfant)
          // Ajout des groupes
          let groupeTrouve = false
          for (const enfantGroupeEnfant of enfant.groupes) {
            if (date.isBetweenDates(new Date(jourPlanning.date), new Date(enfantGroupeEnfant.dateDebut as string), new Date(enfantGroupeEnfant.dateFin as string))) {
              // la date de ce jour est incluse entre les bornes de déclaration de l'association de l'enfant à ce groupe, on peut le rajouter
              const groupeNom = enfantGroupeEnfant.groupe?.nom
              if (groupeNom) {
                this.addEnfantToGroupe(presenceAbsence, groupeNom, enfant)
                groupeTrouve = true
              }
            }
          }
          if (!groupeTrouve) {
            // Si l'enfant ne correspond à aucun groupe, on l'ajoute à "Autres"
            this.addEnfantToGroupe(presenceAbsence, 'Autres', enfant)
          }
        }
      }

      this.joursPlanningAffichables.push({
        jourPlanning,
        jourDate: dateUtils.dateToJourComplet(jourPlanning.date),
        lignes: jourPlanningAffichableLignes,
        presences: presences,
        absences: absences
      })
    }
  }

  private addEnfantToGroupe(presenceAbsence: PresencesAbsences, groupeNom: string, enfant: Enfant) {
    const parGroupe = presenceAbsence.parGroupes[groupeNom] = presenceAbsence.parGroupes[groupeNom] || new Set<Enfant>();
    parGroupe.add(enfant);
  }

  horaireRangeValueToLabel(horaireRange?: number): string | undefined {
    return horaireRange ? parseInt(horaireRange / 60) + 'h' + (horaireRange % 60 || '00') : undefined
  }

  dateStrToHoraireRangeValue(dateStr?: string): number | undefined {
    const date = dateStr ? new Date(dateStr) : undefined
    return date ? date.getHours() * 60 + date.getMinutes() : undefined
  }

  computeCellHoraireClass(jourPlanningAffichableLigne: JourPlanningAffichableLigne, horaireColumn: HoraireColumn): string {
    const classes: string[] = ['cell-horaire']

    if (horaireColumn.minRangeValue % 60 === 0) {
      classes.push('debut-heure')
    } else if (horaireColumn.minRangeValue % 30 === 0) {
      classes.push('debut-demie-heure')
    } else if (horaireColumn.minRangeValue % 15 === 0) {
      classes.push('debut-quart-heure')
    }
    if (horaireColumn.maxRangeValue % 60 === 0) {
      classes.push('fin-heure')
    }
    if (jourPlanningAffichableLigne.horairesRange.min <= horaireColumn.minRangeValue && horaireColumn.maxRangeValue <= jourPlanningAffichableLigne.horairesRange.max) {
      classes.push('personne-presente')
    }

    return classes.join(' ')
  }

}
</script>
