import { Famille } from 'src/interfaces/famille'
import { SimpleAbstractNumberEntityService } from 'src/services/abstract-entity.service'

class FamilleService extends SimpleAbstractNumberEntityService<Famille> {
  constructor () {
    super('/api/familles')
  }
}

const familleService = new FamilleService()

export { familleService }
