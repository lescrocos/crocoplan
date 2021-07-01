import { AbstractService } from 'src/services/abstract.service'

export interface Entity<I extends string|number> {
  id?: I
}

export interface AbstractEntityServiceState<I extends string|number, T extends Entity<I>> {
  all?: T[]
  allById?: Map<I, T>
}

export abstract class AbstractEntityService<I extends string|number, T extends Entity<I>, State extends AbstractEntityServiceState<I, T>> extends AbstractService<T> {

  protected constructor (
    resourcePrefix: string,
    public state: State,
  ) {
    super(resourcePrefix)
  }


  public async getAll () {
    return this.state.all || (this.state.all = await this.findAll())
  }

  public async getAllById () {
    if (!this.state.allById) {
      const all = await this.getAll()
      const allById = new Map<I, T>()
      all.forEach(item => {
        if (item.id !== undefined) {
          allById.set(item.id, item)
        }
      })
      this.state.allById = allById
    }
    return this.state.allById
  }

  public async getById (id: I) {
    const entity = (await this.getAllById()).get(id)
    if (entity === undefined) {
      throw new Error(`Entity ${this.resourcePrefix}/${id} not found`)
    }
    return entity
  }

  public async getByIri (iri: string) {
    return await this.getById(this.iriToId(iri))
  }

  public abstract iriToId(iri: string): I
}

export abstract class SimpleAbstractEntityService<I extends string|number, T extends Entity<I>> extends AbstractEntityService<I, T, AbstractEntityServiceState<I, T>> {

  protected constructor (
    resourcePrefix: string,
    state?: AbstractEntityServiceState<I, T>,
  ) {
    super(resourcePrefix, state || {})
  }

}

export abstract class SimpleAbstractNumberEntityService<T extends Entity<number>> extends SimpleAbstractEntityService<number, T> {

  protected constructor (
    resourcePrefix: string,
    state?: AbstractEntityServiceState<number, T>,
  ) {
    super(resourcePrefix, state)
  }

  iriToId(iri: string): number {
    if (!iri.startsWith(this.resourcePrefix)) {
      throw new Error(`IRI ${iri} does not start with ${this.resourcePrefix}.`)
    }
    return parseInt(iri.substr(this.resourcePrefix.length + 1));
  }
}
