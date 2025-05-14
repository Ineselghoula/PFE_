import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, AbstractControl, ValidationErrors, ValidatorFn } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../auth.service';
import { DomSanitizer } from '@angular/platform-browser';

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css']
})
export class RegisterComponent implements OnInit {
  registerForm!: FormGroup;
  successMessage = '';
  errorMessage = '';
  isLoading = false;
  selectedImage: File | null = null;
  imagePreview: string | null = null;
  showPassword = false;
  passwordStrength = 0;

  constructor(
    private fb: FormBuilder,
    private authService: AuthService,
    private router: Router,
    private sanitizer: DomSanitizer
  ) {}

  ngOnInit(): void {
    this.initForm();
    this.setupPasswordStrengthChecker();
  }

  initForm(): void {
    this.registerForm = this.fb.group({
      first_name: ['', Validators.required],
      last_name: ['', Validators.required],
      email: ['', [Validators.required, Validators.email]],
      phone: [''],
      role: ['participant', Validators.required],
      nom_societe: [''],
      site_web: [''],
      reseau_social: [''],
      biographie: [''],
      date_naissance: [''],
      adresse: [''],
      password: ['', [Validators.required, this.strongPasswordValidator()]],
      confirmPassword: ['', Validators.required],
      acceptTerms: [false, Validators.requiredTrue]
    }, { validators: this.passwordMatchValidator });
  }

  setupPasswordStrengthChecker(): void {
    this.registerForm.get('password')?.valueChanges.subscribe(value => {
      if (!value) {
        this.passwordStrength = 0;
        return;
      }

      const hasUpperCase = /[A-Z]/.test(value);
      const hasLowerCase = /[a-z]/.test(value);
      const hasNumber = /[0-9]/.test(value);
      const hasSpecialChar = /[!@#$%^&*]/.test(value);
      const isLongEnough = value.length >= 8;

      let strength = 0;
      if (isLongEnough) strength++;
      if (hasUpperCase && hasLowerCase) strength++;
      if (hasNumber && hasSpecialChar) strength++;

      this.passwordStrength = strength;
    });
  }

  passwordMatchValidator(group: AbstractControl): ValidationErrors | null {
    const password = group.get('password')?.value;
    const confirm = group.get('confirmPassword')?.value;
    return password === confirm ? null : { mismatch: true };
  }

  strongPasswordValidator(): ValidatorFn {
    return (control: AbstractControl): ValidationErrors | null => {
      const value = control.value;
      if (!value) return null;

      const hasUpperCase = /[A-Z]/.test(value);
      const hasLowerCase = /[a-z]/.test(value);
      const hasNumber = /[0-9]/.test(value);
      const hasSpecialChar = /[!@#$%^&*]/.test(value);
      const isLongEnough = value.length >= 8;

      const valid = hasUpperCase && hasLowerCase && hasNumber && hasSpecialChar && isLongEnough;
      return valid ? null : { weakPassword: true };
    };
  }

  onFileChange(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input?.files?.length) {
      this.selectedImage = input.files[0];
      this.generateImagePreview();
    }
  }

  generateImagePreview(): void {
    const reader = new FileReader();
    reader.onload = () => {
      this.imagePreview = reader.result as string;
    };
    reader.readAsDataURL(this.selectedImage as Blob);
  }

  removeImage(): void {
    this.selectedImage = null;
    this.imagePreview = null;
  }

  togglePasswordVisibility(): void {
    this.showPassword = !this.showPassword;
  }

  async onSubmit(): Promise<void> {
    if (this.registerForm.invalid) {
      this.markFormGroupTouched(this.registerForm);
      this.errorMessage = 'Veuillez remplir correctement tous les champs obligatoires.';
      return;
    }

    this.isLoading = true;
    this.errorMessage = '';
    this.successMessage = '';

    const formData = new FormData();
    Object.entries(this.registerForm.value).forEach(([key, value]) => {
      if (value && key !== 'confirmPassword') {
        formData.append(key, value.toString());
      }
    });

    if (this.selectedImage) {
      formData.append('image', this.selectedImage);
    }

    try {
      await this.authService.register(formData);
      this.successMessage = 'Inscription réussie ! Un email de vérification a été envoyé.';
      setTimeout(() => {
        this.router.navigate(['/verify-email']);
      }, 2000);
    } catch (error: any) {
      console.error('Erreur:', error);
      this.errorMessage = error.error?.message || 'Une erreur est survenue. Veuillez réessayer.';
    } finally {
      this.isLoading = false;
    }
  }

  private markFormGroupTouched(formGroup: FormGroup): void {
    Object.values(formGroup.controls).forEach(control => {
      control.markAsTouched();
      if (control instanceof FormGroup) {
        this.markFormGroupTouched(control);
      }
    });
  }
}