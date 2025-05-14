import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { AuthService } from '../auth.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent {
  loginForm: FormGroup;
  errorMessage: string = '';
  isLoading: boolean = false;
  showPassword: boolean = false;

  constructor(
    private fb: FormBuilder, 
    private authService: AuthService, 
    private router: Router
  ) {
    this.loginForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(6)]],
      rememberMe: [false]
    });
  }

  onSubmit() {
    if (this.loginForm.valid) {
      this.isLoading = true;
      this.errorMessage = '';
      
      this.authService.login(this.loginForm.value).subscribe(
        (response) => {
          localStorage.setItem('access_token', response.access_token);
          
          if (this.loginForm.value.rememberMe) {
            localStorage.setItem('remembered_email', this.loginForm.value.email);
          } else {
            localStorage.removeItem('remembered_email');
          }
          
          this.router.navigate(['/dashboard']);
        },
        (error) => {
          this.isLoading = false;
          this.errorMessage = error.error?.message || 'Échec de la connexion. Veuillez réessayer.';
        },
        () => {
          this.isLoading = false;
        }
      );
    }
  }

  togglePasswordVisibility() {
    this.showPassword = !this.showPassword;
  }
}