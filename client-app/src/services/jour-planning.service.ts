import { httpService } from './http.service';
import { JourPlanning } from 'src/interfaces/jourplanning';

class JourPlanningService {
  async findAllByDateBetween(dateStart: Date, dateEnd: Date): Promise<JourPlanning[]> {
    console.log({dateStart, dateEnd})
    return await httpService.get(`/api/jour-plannings/?date[after]=${dateStart.toISOString()}&date[before]=${dateEnd.toISOString()}&pagination=false`)
  }
}

const jourPlanningService = new JourPlanningService()

export { jourPlanningService }
