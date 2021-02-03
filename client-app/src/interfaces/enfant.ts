import { EnfantGroupeEnfant } from 'src/interfaces/enfant-groupe-enfant';

export interface Enfant {
  '@id'?: string
  nom: string
  dateEntree: Date
  dateSortie: Date
  debutAdaptation?: Date
  finAdaptation?: Date
  famille?: string
  groupes?: EnfantGroupeEnfant[]
  id?: string
}
