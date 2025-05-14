import { Injectable } from '@angular/core';
import { HttpClient, HttpErrorResponse, HttpHeaders } from '@angular/common/http';
import { Router } from '@angular/router';
import { Observable, BehaviorSubject, throwError } from 'rxjs';
import { catchError, tap } from 'rxjs/operators';
import { promises } from 'dns';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private readonly apiUrl = 'http://127.0.0.1:8000/api/auth';
  private readonly logoutUrl = 'http://127.0.0.1:8000/api/logout'; 

  private isAuthenticatedSubject = new BehaviorSubject<boolean>(this.hasToken());
  public isAuthenticated$ = this.isAuthenticatedSubject.asObservable();

  constructor(
    private http: HttpClient,
    private router: Router
  ) {}

  // ✅ Déconnexion
  public logout(): Observable<any> {
    const token = this.getToken();

    if (!token) {
      this.clearAuthData();
      return throwError(() => new Error('Aucun token trouvé.'));
    }

    const headers = new HttpHeaders().set('Authorization', `Bearer ${token}`);

    return this.http.post(this.logoutUrl, {}, { headers }).pipe(
      tap(() => this.clearAuthData()),
      catchError(err => {
        this.clearAuthData(); // Même en cas d'erreur, on supprime le token
        return throwError(() => err);
      })
    );
  }

  // ✅ Déclencher la déconnexion depuis un composant
  public performLogout(): void {
    this.logout().subscribe({
      error: (err) => console.error('Erreur secondaire lors de la déconnexion:', err)
    });
  }

  // ✅ Auth
  public login(credentials: { email: string; password: string }): Observable<{ access_token: string }> {
    return this.http.post<{ access_token: string }>(`${this.apiUrl}/login`, credentials).pipe(
      tap(response => this.handleLoginSuccess(response.access_token)),
      catchError(this.handleError)
    );
  }

  public register(userData: FormData): Promise<any> {
    return this.http.post(`${this.apiUrl}/register`, userData).pipe(
      catchError(this.handleError)
    ).toPromise();
  }

  public verifyEmail(email: string, code: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/verify-email`, { email, code }).pipe(
      catchError(this.handleError)
    );
  }

  public resendVerificationEmail(email: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/resend-verification-code`, { email }).pipe(
      catchError(this.handleError)
    );
  }

  public isAuthenticated(): boolean {
    return this.hasToken();
  }

  public getToken(): string | null {
    return localStorage.getItem('access_token');
  }

  private handleLoginSuccess(token: string): void {
    this.setToken(token);
    this.isAuthenticatedSubject.next(true);
  }

  private setToken(token: string): void {
    localStorage.setItem('access_token', token);
  }

  private clearAuthData(): void {
    localStorage.removeItem('access_token');
    this.isAuthenticatedSubject.next(false);
    this.router.navigate(['/login']);
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

  private handleError(error: HttpErrorResponse): Observable<never> {
    console.error('Erreur API:', error);
    
    let errorMessage = 'Une erreur est survenue, veuillez réessayer.';
    if (error.error instanceof ErrorEvent) {
      errorMessage = `Erreur client: ${error.error.message}`;
    } else if (error.error?.message) {
      errorMessage = error.error.message;
    } else {
      errorMessage = `Erreur serveur ${error.status}: ${error.message}`;
    }

    return throwError(() => new Error(errorMessage));
  }
  getUserRole(): string | null {
    const userData = localStorage.getItem('user');
    if (userData) {
      const user = JSON.parse(userData);
      return user?.role || null;
    }
    return null;
  }
  

  getUserId(): number | null {
    const userData = localStorage.getItem('user');
    if (userData) {
      const user = JSON.parse(userData);
      return user?.id || null;
    }
    return null;
  }


  
}



