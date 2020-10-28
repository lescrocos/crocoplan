import { httpService } from './http.service';
import { MesDisposDuMois } from 'src/interfaces/mesdisposdumois';

class MesDisposDuMoisService {
  async findOneByCodeMoisPlanningAndIdFamille(codeMoisPlanning: string, idFamille: number): Promise<MesDisposDuMois> {
    return await httpService.get(`/api/mes-dispos-du-mois/${codeMoisPlanning}_${idFamille}`)
  }
}

const mesDisposDuMoisService = new MesDisposDuMoisService()

export { mesDisposDuMoisService }
