import { Famille } from 'src/interfaces/famille'
import { AbstractService } from 'src/services/abstract.service'

class FamilleService extends AbstractService<Famille> {
  constructor () {
    super('/api/familles')
  }
}

const familleService = new FamilleService()

export { familleService }
