import { JourPlanning } from './jourplanning';

export interface Garde {
  '@id': string;
  heureArrivee: Date;
  heureDepart: Date;
  commentaire?: string;
  jourPlanning: JourPlanning;
  id: string;
}
