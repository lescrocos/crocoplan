import { httpService } from './http.service';
import { Garde } from 'src/interfaces/garde';

class GardeService {
  async findByFamilleIriAndJourPlanningDateBetween(familleIri: string, jourPlanningDateStart: Date, jourPlanningDateEnd: Date): Promise<Garde[]> {
    return await httpService.get(`/api/gardes?famille=${familleIri}&jourPlanning.date[after]=${jourPlanningDateStart}&jourPlanning.date[before]=${jourPlanningDateEnd}`);
  }
}

const gardeService = new GardeService();

export { gardeService };
