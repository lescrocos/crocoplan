import { Pro } from 'src/interfaces/pro';
import { proService } from 'src/services/pro.service';

export interface ProStoreState {
  pros?: Pro[]
  prosById?: Map<string, Pro>
}

class ProStore {
  public state:ProStoreState = {}

  public async getPros() {
    return this.state.pros || (this.state.pros = await proService.findAll())
  }

  public async getProsById() {
    if (!this.state.prosById) {
      const pros = await this.getPros()
      const prosById = new Map<string, Pro>()
      pros.forEach(pro => prosById.set(pro.id, pro))
      this.state.prosById = prosById
    }
    return this.state.prosById
  }

  public async getProById(proId: string) {
    return (await this.getProsById()).get(proId)
  }
}

const proStore = new ProStore()

export { proStore }
