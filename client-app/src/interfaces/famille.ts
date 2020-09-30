export interface Famille {
  '@id'?: string;
  nom: string;
  dateEntree: Date;
  dateSortie: Date;
  enfants?: string[];
  parents?: string[];
  gardes?: string[];
  gardesDisponibles?: string[];
  id?: string;
}
