import { Component } from '@angular/core';
import { AuthService } from '../auth.service';
import { NgForm } from '@angular/forms';
import { Router } from '@angular/router';

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css']
})
export class RegisterComponent {
  userData = {
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    role: 'participant',
    password: '',
    confirmPassword: '',
    image: null,
    // Organisateur fields
    nom_societe: '',
    site_web: '',
    reseau_social: '',
    biographie: '',
    // Participant fields
    date_naissance: '',
    adresse: ''
  };

  successMessage: string = ''; // Message de succès à afficher après l'inscription

  constructor(private authService: AuthService, private router: Router) {}

  onFileChange(event: any): void {
    const file = event.target.files[0];
    if (file) {
      this.userData.image = file;
    }
  }

  onSubmit(registerForm: NgForm): void {
    if (registerForm.valid) {
      const formData = new FormData();
      
      formData.append('first_name', this.userData.first_name);
      formData.append('last_name', this.userData.last_name);
      formData.append('email', this.userData.email);
      formData.append('phone', this.userData.phone);
      formData.append('role', this.userData.role);
      formData.append('password', this.userData.password);
      formData.append('confirmPassword', this.userData.confirmPassword);
  
      // Image est optionnelle
      if (this.userData.image) {
        formData.append('image', this.userData.image);
      }

      // Ajouter les données spécifiques en fonction du rôle
      if (this.userData.role === 'organisateur') {
        formData.append('nom_societe', this.userData.nom_societe);
        formData.append('site_web', this.userData.site_web);
        formData.append('reseau_social', this.userData.reseau_social);
        formData.append('biographie', this.userData.biographie);
      } else if (this.userData.role === 'participant') {
        formData.append('date_naissance', this.userData.date_naissance);
        formData.append('adresse', this.userData.adresse);
      }

      // Appeler l'API avec formData
      this.authService.register(formData).subscribe(response => {
        console.log(response);
        // Message de succès après l'enregistrement
        this.successMessage = 'Inscription réussie! Veuillez vérifier votre email pour le code de vérification.';
        
        // Réinitialiser le formulaire
        registerForm.reset();

        // Rediriger vers la page de vérification du code d'email
        this.router.navigate(['/verify-email'], { queryParams: { email: this.userData.email } });
      }, error => {
        console.error(error);
        // Gestion des erreurs en cas d'échec de l'inscription
        this.successMessage = 'Une erreur est survenue. Veuillez réessayer.';
      });
    }
  }
}
