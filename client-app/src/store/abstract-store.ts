import { AbstractService } from 'src/services/abstract.service'

export interface Entity {
  id?: string
}

export interface AbstractStoreState<T extends Entity> {
  all?: T[]
  allById?: Map<string, T>
}

export abstract class AbstractStore<T extends Entity, State extends AbstractStoreState<T>, Service extends AbstractService<T>> {
  // eslint-disable-next-line no-useless-constructor
  protected constructor (
    public state: State,
    protected service: Service
  ) {
  }

  public async getAll () {
    return this.state.all || (this.state.all = await this.service.findAll())
  }

  public async getAllById () {
    if (!this.state.allById) {
      const all = await this.getAll()
      const allById = new Map<string, T>()
      all.forEach(item => {
        if (item.id !== undefined) {
          allById.set(item.id, item)
        }
      })
      this.state.allById = allById
    }
    return this.state.allById
  }

  public async getById (id: string) {
    return (await this.getAllById()).get(id)
  }
}
