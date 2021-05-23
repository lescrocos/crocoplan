import { GroupeEnfant } from 'src/interfaces/groupe-enfant'

export interface Contrat {
  '@id'?: string
  id?: string
  dateDebut?: string
  dateFin?: string
  groupe?: GroupeEnfant
}
