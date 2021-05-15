import { Contrat } from 'src/interfaces/contrat';

export interface Enfant {
  '@id'?: string
  nom: string
  dateEntree: Date
  dateSortie: Date
  debutAdaptation?: Date
  finAdaptation?: Date
  famille?: string
  contrats?: Contrat[]
  id?: string
}
