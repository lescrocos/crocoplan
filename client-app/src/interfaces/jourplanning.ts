import { PresencePro } from 'src/interfaces/presencepro';
import { Garde } from 'src/interfaces/garde';
import { PresenceEnfant } from 'src/interfaces/presenceenfant';

export interface JourPlanning {
  '@id'?: string;
  date: Date;
  commentaire?: string;
  moisPlanning?: string;
  presencesEnfants?: PresenceEnfant[];
  presencesPros?: PresencePro[];
  gardes?: Garde[];
  id?: string;
}
