export interface Enfant {
  '@id'?: string;
  nom: string;
  dateEntree: Date;
  dateSortie: Date;
  debutAdaptation?: Date;
  finAdaptation?: Date;
  famille?: string;
  presences?: string[];
  id?: string;
}
