
class HttpService {

  async get<T>(resource: string): Promise<T> {
    const response = await fetch(resource, {headers: {'Accept': 'application/json'}});
    return await response.json();
  }

}

const httpService = new HttpService();

export { httpService };
