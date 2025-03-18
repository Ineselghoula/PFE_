import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ApiService {
  private apiUrl= 'http://localhost:8000/api';

  constructor(private http: HttpClient) { }

  getEvents(): Observable<any> {
    return this.http.get(`${this.apiUrl}/events`);
  }

  searchEvents(filters: any): Observable<any>{
    return this.http.post(`${this.apiUrl}/evenements/search`,filters);
  }
}
