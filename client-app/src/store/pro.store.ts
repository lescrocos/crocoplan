import { Pro } from 'src/interfaces/pro'
import { ProService, proService } from 'src/services/pro.service'
import { AbstractStore, AbstractStoreState } from 'src/store/abstract-store'

export type ProStoreState = AbstractStoreState<Pro>

class ProStore extends AbstractStore<Pro, ProStoreState, ProService> {
  constructor () {
    super({}, proService)
  }
}

const proStore = new ProStore()

export { proStore }
