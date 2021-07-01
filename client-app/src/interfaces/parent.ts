import { Utilisateur } from 'src/interfaces/utilisateur'

export interface Parent extends Utilisateur {
  famille?: string;
}
