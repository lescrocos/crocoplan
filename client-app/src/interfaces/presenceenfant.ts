import { Enfant } from 'src/interfaces/enfant';

export interface PresenceEnfant {
  '@id'?: string;
  present?: string;
  absenceType?: string;
  heureArrivee?: Date;
  heureDepart?: Date;
  commentaire?: string;
  version?: Date;
  jourPlanning?: string;
  enfant: Enfant;
  id?: string;
}
