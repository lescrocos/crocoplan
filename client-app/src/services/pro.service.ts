import { AbstractService } from 'src/services/abstract.service';
import { Pro } from 'src/interfaces/pro';

class ProService extends AbstractService<Pro> {
  constructor() {
    super('/api/pros');
  }
}

const proService = new ProService()

export { proService }
