import { MoisPlanning } from 'src/interfaces/moisplanning';
import { CommentaireFamilleMoisPlanning } from 'src/interfaces/commentairefamillemoisplanning';
import { GardeDisponible } from 'src/interfaces/garde-disponible';

export interface MesDisposDuMois {
  '@id'?: string;
  moisPlanning?: MoisPlanning;
  commentaireFamilleMoisPlanning?: CommentaireFamilleMoisPlanning;
  gardesDisponibles?: GardeDisponible[];
}
