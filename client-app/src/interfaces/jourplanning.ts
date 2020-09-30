export interface JourPlanning {
  '@id'?: string;
  date: Date;
  commentaire?: string;
  moisPlanning?: string;
  presencesEnfants?: string[];
  presencesPros?: string[];
  gardes?: string[];
  id?: string;
}
