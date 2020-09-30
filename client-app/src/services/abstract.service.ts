import { httpService } from './http.service'
import { Famille } from 'src/interfaces/famille'

abstract class AbstractService<T> {
  private resourcePrefix: string

  protected constructor(resourcePrefix: string) {
    this.resourcePrefix = resourcePrefix
  }

  async findAll(): Promise<T[]> {
    return await httpService.get(`${this.resourcePrefix}`)
  }
}

export { AbstractService }
