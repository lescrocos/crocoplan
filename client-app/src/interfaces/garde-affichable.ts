import { Garde } from 'src/interfaces/garde'

export interface GardeAffichable {
  jour: string;
  heureArrivee: string;
  heureDepart: string;
  garde?: Garde;
}
