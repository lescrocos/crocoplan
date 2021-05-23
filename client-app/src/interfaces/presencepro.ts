import { Pro } from 'src/interfaces/pro'

export interface PresencePro {
  '@id'?: string;
  present: boolean;
  absenceType?: string;
  heureArrivee?: string;
  heureDepart?: string;
  commentaire?: string;
  version?: string;
  jourPlanning?: string;
  pro?: string | Pro;
  id?: string;
}
