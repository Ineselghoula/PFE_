import { Injectable } from '@angular/core';
import { HttpClient, HttpErrorResponse, HttpHeaders } from '@angular/common/http';
import { Observable, BehaviorSubject, throwError } from 'rxjs';
import { catchError, tap } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private apiUrl = 'http://127.0.0.1:8000/api/auth';
  private isAuthenticatedSubject = new BehaviorSubject<boolean>(this.hasToken());
  isAuthenticated$ = this.isAuthenticatedSubject.asObservable();

  constructor(private http: HttpClient) {}

  register(userData: FormData): Observable<any> {
    return this.http.post(`${this.apiUrl}/register`, userData)
      .pipe(catchError(this.handleError));
  }

  verifyEmail(email: string, code: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/verify-email`, { email, code })
      .pipe(catchError(this.handleError));
  }

  resendVerificationEmail(email: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/resend-verification-code`, { email })
      .pipe(catchError(this.handleError));
  }

  login(credentials: { email: string; password: string; }): Observable<{ access_token: string }> {
    return this.http.post<{ access_token: string }>(`${this.apiUrl}/login`, credentials)
      .pipe(
        tap(response => this.setToken(response.access_token)),
        catchError(this.handleError)
      );
  }

  logout(): void {
    this.clearToken();
  }

  logoutFromServer(): Observable<any> {
    return this.http.post(`${this.apiUrl}/logout`, {}, { headers: this.getAuthHeaders() })
      .pipe(
        tap(() => this.clearToken()),
        catchError(this.handleError)
      );
  }

  isAuthenticated(): boolean {
    return this.hasToken();
  }

  private setToken(token: string): void {
    localStorage.setItem('access_token', token);
    this.isAuthenticatedSubject.next(true);
  }

  private clearToken(): void {
    localStorage.removeItem('access_token');
    this.isAuthenticatedSubject.next(false);
  }

  getToken(): string | null {
    return localStorage.getItem('access_token');
  }

  private hasToken(): boolean {
    return !!this.getToken();
  }

  private getAuthHeaders(): HttpHeaders {
    const token = this.getToken();
    return new HttpHeaders({
      'Authorization': `Bearer ${token}`
    });
  }

  private handleError(error: HttpErrorResponse) {
    console.error('Erreur API:', error);
    
    let errorMessage = 'Une erreur est survenue, veuillez réessayer.';
    if (error.error instanceof ErrorEvent) {
      errorMessage = `Erreur client : ${error.error.message}`;
    } else {
      errorMessage = `Erreur serveur ${error.status}: ${error.message}`;
    }

    return throwError(() => new Error(errorMessage));
  }
 

}
