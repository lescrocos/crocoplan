class HttpService {
  async get<T> (resource: string): Promise<T> {
    const response = await fetch(resource, { headers: { Accept: 'application/json' } })
    return await response.json() as T
  }

  async put<T> (resource: string, body: any): Promise<T> {
    const response = await fetch(resource, {
      method: 'PUT',
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(body)
    })
    return await response.json() as T
  }
}

const httpService = new HttpService()

export { httpService }
