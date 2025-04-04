import { Component } from '@angular/core';
import { NgForm } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../auth.service';

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css'],
})
export class RegisterComponent {
  userData = {
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    role: 'participant',
    nom_societe: '',
    site_web: '',
    reseau_social: '',
    biographie: '',
    date_naissance: '',
    adresse: '',
    password: '',
    confirmPassword: '',
    image: null as File | null,
  };

  successMessage: string = '';
  errorMessage: string = '';
  isLoading: boolean = false;

  constructor(private authService: AuthService, private router: Router) {}

  onFileChange(event: Event) {
    const input = event.target as HTMLInputElement;
    if (input?.files?.length) {
      this.userData.image = input.files[0];
    }
  }

  onSubmit(registerForm: NgForm) {
    if (registerForm.invalid) {
      this.errorMessage = 'Veuillez remplir correctement tous les champs.';
      return;
    }

    if (this.userData.password !== this.userData.confirmPassword) {
      this.errorMessage = 'Les mots de passe ne correspondent pas.';
      return;
    }

    this.isLoading = true;
    this.errorMessage = '';
    this.successMessage = '';

    const formData = new FormData();
    Object.entries(this.userData).forEach(([key, value]) => {
      if (value && key !== 'image') {
        formData.append(key, value as string);
      }
    });

    if (this.userData.image) {
      formData.append('image', this.userData.image);
    }

    this.authService.register(formData).subscribe(
      () => {
        this.successMessage = 'Inscription réussie! Un email de vérification a été envoyé.';
        this.isLoading = false;
        console.log('Redirection vers /verify-email...');
        setTimeout(() => {
          this.router.navigate(['/verify-email']); // ✅ Vérifie que cette ligne s'exécute
        }, 1500);
      },
      (error) => {
        console.error('Erreur:', error);
        this.errorMessage = 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.';
        this.isLoading = false;
      }
    );
  }
}
