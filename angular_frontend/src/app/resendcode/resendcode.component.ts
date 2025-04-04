import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { AuthService } from '../auth.service';

@Component({
  selector: 'app-resend-code',
  templateUrl: './resendcode.component.html',
  styleUrls: ['./resendcode.component.css']
})
export class ResendCodeComponent {
  resendForm: FormGroup;
  message: string = '';

  constructor(private fb: FormBuilder, private authService: AuthService) {
    this.resendForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
    });
  }

  onSubmit() {
    if (this.resendForm.invalid) {
      return;
    }

    const formData = this.resendForm.value;

    this.authService.resendVerificationEmail(formData).subscribe(
      (response: any) => {
        this.message = 'Nouveau code envoyé à votre e-mail.';
      },
      (error: any) => {
        this.message = error.error.message || 'Erreur lors de l\'envoi du code.';
      }
    );
  }
}
