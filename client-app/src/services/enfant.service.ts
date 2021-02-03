import { AbstractService } from 'src/services/abstract.service';
import { Enfant } from 'src/interfaces/enfant';

export class EnfantService extends AbstractService<Enfant> {
  constructor() {
    super('/api/enfants');
  }
}

const enfantService = new EnfantService()

export { enfantService }
