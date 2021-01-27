import { Famille } from 'src/interfaces/famille';
import { familleService } from 'src/services/famille.service';

export interface FamilleStoreState {
  familleSelectionnee?: Famille
  famillesById?: Map<string, Famille>
}

class FamilleStore {
  public state:FamilleStoreState = {familleSelectionnee: {}}

  public selectionneFamille(famille: Famille) {
    this.state.familleSelectionnee = famille
  }

  public async getFamilleById(familleId: string | undefined): Promise<Famille | undefined> {
    if (familleId) {
      if (!this.state.famillesById) {
        // Chargement de la liste des familles
        const famillesById: Map<string, Famille> = new Map<string, Famille>()
        const familles = await familleService.findAll()
        familles.forEach(famille => famillesById.set(famille.id || '', famille))
        this.state.famillesById = famillesById
      }
      return this.state.famillesById.get(familleId)
    }
  }
}

const familleStore = new FamilleStore()

export { familleStore }
