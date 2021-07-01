import { authentificationService } from 'src/services/authentification.service';

class HttpService {
  static jwt: string | null = null
  static tokenResponse: Promise<string> | null = null

  async get<T> (resource: string): Promise<T> {
    return this.handleFetch(resource, {
      headers: this.createHeaders()
    })
  }

  async handleFetch<T> (resource: string, init?: RequestInit): Promise<T> {
    const response = await fetch(resource, init)
    if (!response.ok) {
      const responseJson = await response.json()
      if (response.status === 401 && responseJson.message === 'Expired JWT Token') {
        authentificationService.logout()
      }
      throw new Error('Erreur ' + response.status + ' (' + response.statusText + ' ' + JSON.stringify(responseJson) + ')')
    }
    return await response.json() as T
  }

  private createHeaders(headers?: any) {
    headers = {
      Accept: 'application/json',
      ...(headers || {})
    }
    if (authentificationService.state.jwt) {
      headers['Authorization'] = 'Bearer ' + authentificationService.state.jwt
    }
    return headers
  }

  async put<T> (resource: string, body: any): Promise<T> {
    return this.handleFetch(resource, {
      method: 'PUT',
      headers: this.createHeaders({
        'Content-Type': 'application/json',
      }),
      body: JSON.stringify(body)
    })
  }

  async post<T> (resource: string, body: any): Promise<T> {
    return this.handleFetch(resource, {
      method: 'POST',
      headers: this.createHeaders({
        'Content-Type': 'application/json',
      }),
      body: JSON.stringify(body)
    })
  }
}

const httpService = new HttpService()

export { httpService }
