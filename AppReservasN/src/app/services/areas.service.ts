import { Injectable } from '@angular/core';
import {environment} from '../../environments/environment';
import {HttpClient, HttpHeaders} from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class AreasService {

  apiUrl = environment.apiUrl;
  constructor(private http: HttpClient) { }

  protected generateAuthbasicHeaders(): HttpHeaders{
    return new HttpHeaders({
      'Content-Type':'aplication/json'
    });
  }
  getAreas(): any{
    return this.http.get<any>(this.apiUrl+"/areas");
  }

  insertarAreas(area){
    return this.http.post<any>(this.apiUrl+"/areas",
    JSON.stringify(area),
    {headers: this.generateAuthbasicHeaders()});
  }
}
