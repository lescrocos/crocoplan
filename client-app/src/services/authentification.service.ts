import { Utilisateur } from 'src/interfaces/utilisateur'
import { httpService } from 'src/services/http.service';
import { LocalStorage, Notify } from 'quasar';

export class AuthentificationServiceState {
  jwt: string | null
  utilisateur?: Utilisateur | null
}

export class AuthentificationService {
  public state: AuthentificationServiceState

  constructor() {
    this.state = {
      jwt: LocalStorage.getItem('jwt'),
      utilisateur: LocalStorage.getItem('utilisateur')
    }
  }

  async login(email: string, password: string) {
    try {
      const authResponse = await httpService.post<{ token: string, utilisateur: Utilisateur }>('/api/jwt-auth', {email, password})
      LocalStorage.set('jwt', authResponse.token)
      LocalStorage.set('utilisateur', authResponse.utilisateur)
      this.state.jwt = authResponse.token
      this.state.utilisateur = authResponse.utilisateur
    } catch (err) {
      localStorage.removeItem('jwt')
      localStorage.removeItem('utilisateur')
      throw err
    }
  }
}

const authentificationService = new AuthentificationService()

export { authentificationService }
