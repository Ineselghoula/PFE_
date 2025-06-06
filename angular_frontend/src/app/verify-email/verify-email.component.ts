import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { AuthService } from '../auth.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-verify-email',
  templateUrl: './verify-email.component.html',
  styleUrls: ['./verify-email.component.css']
})
export class VerifyEmailComponent {
  verifyForm: FormGroup;
  message: string = '';
  error: string = '';
  isVerifying: boolean = false;

  constructor(
    private fb: FormBuilder, 
    private authService: AuthService, 
    private router: Router
  ) {
    this.verifyForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      verification_code: ['', [
        Validators.required, 
        Validators.minLength(4), 
        Validators.maxLength(4),
        Validators.pattern(/^[0-9]*$/)
      ]]
    });
  }

  verifyEmail() {
    if (this.verifyForm.invalid) {
      this.error = 'Veuillez remplir tous les champs correctement.';
      return;
    }

    this.isVerifying = true;
    this.error = '';
    this.message = '';

    const { email, verification_code } = this.verifyForm.value;

    this.authService.verifyEmail(email, verification_code).subscribe({
      next: (response) => {
        this.message = 'Vérification réussie ! Redirection vers la connexion...';
        setTimeout(() => this.router.navigate(['/login']), 2000);
      },
      error: (err) => {
        console.error('Erreur de vérification:', err);
        this.error = err.error?.message || 'Code invalide ou expiré. Veuillez réessayer.';
      },
      complete: () => {
        this.isVerifying = false;
      }
    });
  }
}