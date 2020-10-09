import { httpService } from './http.service'
import { Garde } from 'src/interfaces/garde'

class GardeService {
  async findAllByFamilleIriAndJourPlanningDateBetween(familleIri: string, jourPlanningDateStart: Date, jourPlanningDateEnd: Date): Promise<Garde[]> {
    return await httpService.get(`/api/gardes?famille=${familleIri}&jourPlanning.date[after]=${jourPlanningDateStart.toISOString()}&jourPlanning.date[before]=${jourPlanningDateEnd.toISOString()}&pagination=false`)
  }
}

const gardeService = new GardeService()

export { gardeService }
