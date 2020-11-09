import { httpService } from './http.service';
import { MesDisposDuMois, MesDisposDuMoisUpdate } from 'src/interfaces/mesdisposdumois';

class MesDisposDuMoisService {
  async findOneByCodeMoisPlanningAndIdFamille(codeMoisPlanning: string, idFamille: string): Promise<MesDisposDuMois> {
    return await httpService.get(`/api/mes-dispos-du-mois/${codeMoisPlanning}_${idFamille}`)
  }

  async update(mesDisposDuMoisUpdate: MesDisposDuMoisUpdate): Promise<MesDisposDuMois> {
    return await httpService.put(`/api/mes-dispos-du-mois/${mesDisposDuMoisUpdate.code}`, mesDisposDuMoisUpdate)
  }
}

const mesDisposDuMoisService = new MesDisposDuMoisService()

export { mesDisposDuMoisService }
