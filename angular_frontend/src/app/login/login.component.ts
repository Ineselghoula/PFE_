import { Component } from '@angular/core';
import { AuthService } from '../auth.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css'],
})
export class LoginComponent {
  credentials = {
    email: '',
    password: '',
  };

  constructor(private authService: AuthService, private router: Router) {}

  onSubmit() {
    this.authService.login(this.credentials).subscribe(
      (response) => {
        localStorage.setItem('access_token', response.access_token);
        alert('Connexion réussie.');
        this.router.navigate(['/dashboard']);
      },
      (error) => {
        alert('Erreur lors de la connexion: ' + error.error.message);
      }
    );
  }
}