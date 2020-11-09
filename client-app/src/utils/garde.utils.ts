import { Garde } from 'src/interfaces/garde';
import { GardeAffichable } from 'src/interfaces/garde-affichable';
import { dateUtils } from 'src/utils/date.utils';

export namespace gardeUtils {

  export function gardeToGardeAffichable(garde: Garde): GardeAffichable {
    return {
      jour: dateUtils.dateToJourComplet(garde.jourPlanning.date),
      heureArrivee: dateUtils.dateToHeure(garde.heureArrivee),
      heureDepart: dateUtils.dateToHeure(garde.heureDepart),
      garde: garde
    }
  }

  export function gardesToGardeAffichableMappedByGardeId(gardes: Garde[]): Map<string, GardeAffichable> {
    const map = new Map<string, GardeAffichable>()
    gardes.forEach(garde => map.set(garde.id, gardeToGardeAffichable(garde)))
    return map
  }

}
