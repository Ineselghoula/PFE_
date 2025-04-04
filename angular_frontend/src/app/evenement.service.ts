import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ApiService {
  private apiUrl = 'http://localhost:8000/api';  

  constructor(private http: HttpClient) { }

  // Assurez-vous que la méthode retourne un Observable
  getEvents(): Observable<any> {
    return this.http.get(`${this.apiUrl}/events`);
  }
}