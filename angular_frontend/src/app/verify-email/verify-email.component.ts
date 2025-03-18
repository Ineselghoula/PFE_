import { Component } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthService } from '../auth.service';

@Component({
  selector: 'app-verify-email',
  templateUrl: './verify-email.component.html',
  styleUrls: ['./verify-email.component.css']
})
export class VerifyEmailComponent {
  email: string = '';
  code: string = '';
  successMessage: string = '';
  errorMessage: string = '';

  constructor(
    private authService: AuthService, 
    private router: Router,
    private route: ActivatedRoute
  ) {
    // Récupérer l'email de la query string
    this.route.queryParams.subscribe(params => {
      this.email = params['email'];
    });
  }

  onSubmit() {
    if (this.email && this.code) {
      this.authService.verifyEmail(this.email, this.code).subscribe(
        (response) => {
          this.successMessage = response.message;
          this.errorMessage = '';
          // Rediriger vers la page d'accueil ou dashboard après la vérification
          this.router.navigate(['/home']);
        },
        (error) => {
          this.errorMessage = error.error.message;
          this.successMessage = '';
        }
      );
    }
  }
}
