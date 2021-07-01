import { Utilisateur } from 'src/interfaces/utilisateur'
import { httpService } from 'src/services/http.service';
import { LocalStorage, Notify } from 'quasar';
import { Famille } from 'src/interfaces/famille';
import { familleService } from 'src/services/famille.service';
import { Parent } from 'src/interfaces/parent';

export class AuthentificationServiceState {
  jwt: string | null
  utilisateur: Utilisateur | null
  famille: Famille | null
}

export class AuthentificationService {
  public state: AuthentificationServiceState

  constructor() {
    this.state = {
      jwt: LocalStorage.getItem('jwt'),
      utilisateur: LocalStorage.getItem('utilisateur'),
      famille: LocalStorage.getItem('famille')
    }
  }

  async login(email: string, password: string) {
    try {
      const authResponse = await httpService.post<{ token: string, utilisateur: Utilisateur|Parent }>('/api/jwt-auth', {email, password})
      LocalStorage.set('jwt', authResponse.token)
      this.state.jwt = authResponse.token
      let famille: Famille | null = null
      if ('famille' in authResponse.utilisateur && authResponse.utilisateur.famille) {
        famille = await familleService.getByIri(authResponse.utilisateur.famille)
      }
      LocalStorage.set('utilisateur', authResponse.utilisateur)
      LocalStorage.set('famille', famille)
      this.state.utilisateur = authResponse.utilisateur
      this.state.famille = famille
    } catch (err) {
      this.logout()
      throw err
    }
  }

  logout() {
    LocalStorage.remove('jwt')
    LocalStorage.remove('utilisateur')
    LocalStorage.remove('famille')
    this.state.jwt = null
    this.state.utilisateur = null
    this.state.famille = null
  }
}

const authentificationService = new AuthentificationService()

export { authentificationService }
