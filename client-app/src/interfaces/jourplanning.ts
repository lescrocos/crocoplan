import { PresencePro } from 'src/interfaces/presencepro';

export interface JourPlanning {
  '@id'?: string;
  date: Date;
  commentaire?: string;
  moisPlanning?: string;
  presencesEnfants?: string[];
  presencesPros?: PresencePro[];
  gardes?: string[];
  id?: string;
}
