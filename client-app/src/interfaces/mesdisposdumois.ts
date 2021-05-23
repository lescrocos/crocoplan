import { MoisPlanning } from 'src/interfaces/moisplanning'
import { CommentaireFamilleMoisPlanning } from 'src/interfaces/commentairefamillemoisplanning'
import { Garde } from 'src/interfaces/garde'

export interface MesDisposDuMoisUpdate {
  code?: string;
  gardesDisponiblesIds?: string[];
}

export interface MesDisposDuMois extends MesDisposDuMoisUpdate {
  '@id'?: string;
  moisPlanning?: MoisPlanning;
  commentaireFamilleMoisPlanning?: CommentaireFamilleMoisPlanning;
  gardes: Garde[];
}
