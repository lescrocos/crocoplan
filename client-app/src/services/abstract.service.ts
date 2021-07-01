import { httpService } from './http.service'

abstract class AbstractService<T> {
  protected readonly resourcePrefix: string

  protected constructor (resourcePrefix: string) {
    this.resourcePrefix = resourcePrefix
  }

  async findAll (): Promise<T[]> {
    return await httpService.get(`${this.resourcePrefix}`)
  }
}

export { AbstractService }
