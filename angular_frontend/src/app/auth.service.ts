import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private apiUrl = 'http://127.0.0.1:8000/api'; // URL de base de votre backend Laravel

  constructor(private http: HttpClient) {}

  // Méthode pour l'enregistrement
  register(data: FormData): Observable<any> {
    return this.http.post(`${this.apiUrl}/register`, data);
  }

  // Méthode pour la connexion
  login(credentials: { email: string, password: string }): Observable<any> {
    return this.http.post(`${this.apiUrl}/login`, credentials);
  }

  verifyEmail(email: string, code: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/verify-email`, { email, code });
  }
  

  // Méthode pour vérifier le code de vérification
  verifyCode(code: string): Observable<any> {
    const body = { code }; // Envoie seulement le code
    return this.http.post(`${this.apiUrl}/verify-code`, body); // URL du backend pour vérifier le code
  }
}
