import { PresencePro } from 'src/interfaces/presencepro';
import { Garde } from 'src/interfaces/garde';

export interface JourPlanning {
  '@id'?: string;
  date: Date;
  commentaire?: string;
  moisPlanning?: string;
  presencesEnfants?: string[];
  presencesPros?: PresencePro[];
  gardes?: Garde[];
  id?: string;
}
