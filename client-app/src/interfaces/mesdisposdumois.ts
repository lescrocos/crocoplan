import { MoisPlanning } from 'src/interfaces/moisplanning';
import { CommentaireFamilleMoisPlanning } from 'src/interfaces/commentairefamillemoisplanning';
import { GardeDisponible } from 'src/interfaces/garde-disponible';

export interface MesDisposDuMoisUpdate {
  code?: string;
  gardesDisponiblesIds?: string[];
}

export interface MesDisposDuMois {
  '@id'?: string;
  code?: string;
  moisPlanning?: MoisPlanning;
  commentaireFamilleMoisPlanning?: CommentaireFamilleMoisPlanning;
  gardesDisponibles?: GardeDisponible[];
}
