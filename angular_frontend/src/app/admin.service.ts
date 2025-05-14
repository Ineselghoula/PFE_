// src/app/admin.service.ts
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AdminService {
  private apiUrl = 'http://127.0.0.1:8000/api/auth';

  constructor(private http: HttpClient) { }

  getDashboardStats(): Observable<any> {
    return this.http.get(`${this.apiUrl}/dashboard`);
  }

  getMonthlyStats(): Observable<any> {
    return this.http.get(`${this.apiUrl}/monthly-stats`);
  }

  getAllUsers(): Observable<any> {
    return this.http.get(`${this.apiUrl}/admin/users`);
  }

  // ← Nouvelle méthode pour approuver un événement
  approveEvent(eventId: number): Observable<any> {
    // J'imagine que ton backend attend un POST vers /events/{id}/approve
    return this.http.post(`${this.apiUrl}/evenements/${eventId}/approve`, {});
  }
}
