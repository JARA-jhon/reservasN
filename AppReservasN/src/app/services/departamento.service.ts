import { Injectable } from '@angular/core';
import {environment} from '../../environments/environment';
import {HttpClient, HttpHeaders} from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class DepartamentoService {
  apiUrl = environment.apiUrl;
  constructor(private http: HttpClient) { }

  protected generateAuthbasicHeaders(): HttpHeaders{
    return new HttpHeaders({
      'Content-Type':'aplication/json'
    });
  }
  getDepartamentos(): any{
    return this.http.get<any>(this.apiUrl+"/departamentos");
  }

  insertarDepartamento(departamento){
    return this.http.post<any>(this.apiUrl+"/departamentos",
    JSON.stringify(departamento),
    {headers: this.generateAuthbasicHeaders()});
  }
}
