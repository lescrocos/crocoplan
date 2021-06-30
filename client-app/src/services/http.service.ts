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
      throw new Error('Erreur ' + response.status + ' (' + response.statusText + ' ' + JSON.stringify(await response.json()) + ')')
    }
    return await response.json() as T
  }

  private createHeaders(headers?: any) {
    headers = {
      Accept: 'application/json',
      ...(headers || {})
    }
    console.log({authentificationState: authentificationService.state})
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
