class HttpService {
  static jwt: string | null = null
  static tokenResponse: Promise<string> | null = null

  async get<T> (resource: string): Promise<T> {
    const response = await fetch(resource, {
      headers: {
        Accept: 'application/json',
        Authorization: await this.getAuthorizationHeaderValue()
      }
    })
    return await response.json() as T
  }

  async put<T> (resource: string, body: any): Promise<T> {
    const response = await fetch(resource, {
      method: 'PUT',
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        Authorization: await this.getAuthorizationHeaderValue()
      },
      body: JSON.stringify(body)
    })
    return await response.json() as T
  }

  async getAuthorizationHeaderValue (): Promise<string> {
    if (!HttpService.jwt) {
      if (!HttpService.tokenResponse) {
        HttpService.tokenResponse = (async function () {
          const response = await fetch('/api/jwt-auth', {
            method: 'POST',
            headers: {
              Accept: 'application/json',
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              email: 'admin@planning.lescrocos.fr',
              password: 'admin'
            })
          })
          const tokenResponse = await response.json() as { token: string }
          HttpService.tokenResponse = null
          return tokenResponse.token
        })()
      }
      HttpService.jwt = await HttpService.tokenResponse
    }
    return 'Bearer ' + HttpService.jwt
  }
}

const httpService = new HttpService()

export { httpService }
