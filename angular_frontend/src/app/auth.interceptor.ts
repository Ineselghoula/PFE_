import { Injectable } from '@angular/core';
import { HttpInterceptor, HttpRequest, HttpHandler, HttpEvent } from '@angular/common/http';
import { Observable } from 'rxjs';
import { AuthService } from './auth.service';

@Injectable()
export class AuthInterceptor implements HttpInterceptor {

  constructor(private authService: AuthService) {}

  intercept(req: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    const token = this.authService.getToken();  // Récupérer le token d'authentification depuis le service

    if (token) {
      req = req.clone({
        setHeaders: {
          Authorization: `Bearer ${token}`  // Ajouter le token dans les en-têtes de la requête
        }
      });
    }

    return next.handle(req);  // Passer la requête modifiée ou originale
  }
}
