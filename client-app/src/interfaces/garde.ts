import { JourPlanning } from './jourplanning'
import { Famille } from 'src/interfaces/famille'

export interface Garde {
  '@id': string;
  heureArrivee: string;
  heureDepart: string;
  commentaire?: string;
  jourPlanning: JourPlanning;
  famille?: Famille;
  id: string;
}
