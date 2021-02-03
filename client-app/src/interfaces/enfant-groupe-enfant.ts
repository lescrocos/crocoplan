import { GroupeEnfant } from 'src/interfaces/groupe-enfant';

export interface EnfantGroupeEnfant {
  '@id'?: string
  id?: string
  dateDebut?: string
  dateFin?: string
  groupe?: GroupeEnfant
}
