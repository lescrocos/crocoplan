import { Garde } from 'src/interfaces/garde'
import { GardeAffichable } from 'src/interfaces/garde-affichable'
import DateUtils from 'src/utils/date.utils'

export default class GardeUtils {
  static gardeToGardeAffichable (garde: Garde): GardeAffichable {
    return {
      jour: DateUtils.dateToJourComplet(garde.jourPlanning.date),
      heureArrivee: DateUtils.dateToHeure(garde.heureArrivee),
      heureDepart: DateUtils.dateToHeure(garde.heureDepart),
      garde: garde
    }
  }

  static gardesToGardeAffichableMappedByGardeId (gardes: Garde[]): Map<string, GardeAffichable> {
    const map = new Map<string, GardeAffichable>()
    gardes.forEach(garde => map.set(garde.id, this.gardeToGardeAffichable(garde)))
    return map
  }
}
