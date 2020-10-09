import { Famille } from 'src/interfaces/famille';

export interface FamilleStoreState {
  familleSelectionnee: Famille
}

class FamilleStore {
  public state:FamilleStoreState = {familleSelectionnee: {}}

  public selectionneFamille(famille: Famille) {
    this.state.familleSelectionnee = famille
  }

}

const familleStore = new FamilleStore()

export { familleStore }
