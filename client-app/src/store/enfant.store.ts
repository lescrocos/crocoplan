import { Enfant } from 'src/interfaces/enfant';
import { EnfantService, enfantService } from 'src/services/enfant.service';
import { AbstractStore, AbstractStoreState } from 'src/store/abstract-store';

export interface EnfantStoreState extends AbstractStoreState<Enfant> {
}

class EnfantStore extends AbstractStore<Enfant, EnfantStoreState, EnfantService>{

  constructor() {
    super({}, enfantService);
  }

}

const enfantStore = new EnfantStore()

export { enfantStore }
