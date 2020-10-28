import { httpService } from './http.service'
import { Garde } from 'src/interfaces/garde'

class GardeService {
  async findAllByFamilleIriAndJourPlanningDateBetween(familleIri: string | null, jourPlanningDateStart: Date, jourPlanningDateEnd: Date): Promise<Garde[]> {
    const familleSelector = familleIri == null ? 'exists[famille]=false' : `famille=${familleIri}`;
    return await httpService.get(`/api/gardes?${familleSelector}&jourPlanning.date[after]=${jourPlanningDateStart.toISOString()}&jourPlanning.date[before]=${jourPlanningDateEnd.toISOString()}&pagination=false`)
  }
}

const gardeService = new GardeService()

export { gardeService }
