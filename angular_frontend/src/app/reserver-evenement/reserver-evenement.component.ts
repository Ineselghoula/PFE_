import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Router } from '@angular/router';
import { AuthService } from '../auth.service';

@Component({
  selector: 'app-reserver-evenement',
  templateUrl: './reserver-evenement.component.html',
  styleUrls: ['./reserver-evenement.component.css']
})
export class ReserverEvenementComponent implements OnInit {
  @Input() event: any;
  @Output() reservationComplete = new EventEmitter<{success: boolean, remainingPlaces: number}>();
  @Output() closeModal = new EventEmitter<void>();

  reservationForm: FormGroup;
  isLoading = false;
  reservationSuccess = false;
  reservationError = '';
  reservationCode = '';

  private apiUrl = 'http://127.0.0.1:8000/api/auth';

  constructor(
    private fb: FormBuilder,
    private http: HttpClient,
    private router: Router,
    private authService: AuthService
  ) {
    this.reservationForm = this.fb.group({
      evenement_id: ['', Validators.required],
      full_name: ['', [Validators.required, Validators.minLength(3)]],
      numero_telephone: ['', [Validators.required, Validators.pattern(/^[0-9]{8,15}$/)]],
      email: ['', [Validators.required, Validators.email]],
      quantity: [1, [Validators.required, Validators.min(1)]]
    });
  }

  ngOnInit(): void {
    if (this.event) {
      this.reservationForm.patchValue({
        evenement_id: this.event.id,
        quantity: 1
      });

      // Set max validator for quantity based on available places
      const quantityControl = this.reservationForm.get('quantity');
      if (quantityControl) {
        quantityControl.addValidators(Validators.max(this.event.nbr_place));
        quantityControl.updateValueAndValidity();
      }

      if (this.authService.isAuthenticated()) {
        this.prefillUserData();
      }
    }
  }

  prefillUserData(): void {
    const token = this.authService.getToken();
    if (!token) return;

    const headers = new HttpHeaders().set('Authorization', `Bearer ${token}`);

    this.http.get<any>(`${this.apiUrl}/user/profile`, { headers }).subscribe(
      (res) => {
        this.reservationForm.patchValue({
          full_name: res.user?.name || '',
          email: res.user?.email || '',
          numero_telephone: res.user?.phone || ''
        });
      },
      (err) => {
        console.error('Erreur lors de la récupération du profil:', err);
      }
    );
  }

  get f() {
    return this.reservationForm.controls;
  }

  async onReserve(): Promise<void> {
    if (this.reservationForm.invalid) {
      this.markFormGroupTouched(this.reservationForm);
      return;
    }

    this.isLoading = true;
    this.reservationForm.disable();
    this.reservationError = '';

    try {
      if (!this.authService.isAuthenticated()) {
        throw new Error('Vous devez être connecté pour effectuer une réservation');
      }

      const token = this.authService.getToken();
      if (!token) {
        throw new Error('Erreur d\'authentification');
      }

      const formData = this.reservationForm.value;

      const headers = new HttpHeaders({
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      });

      const response: any = await this.http.post(
        `${this.apiUrl}/reserve-event`, 
        formData, 
        { headers }
      ).toPromise();

      this.handleReservationSuccess(response);
    } catch (error: any) {
      this.handleReservationError(error);
    } finally {
      this.isLoading = false;
      this.reservationForm.enable();
    }
  }

  private markFormGroupTouched(formGroup: FormGroup) {
    Object.values(formGroup.controls).forEach(control => {
      control.markAsTouched();

      if (control instanceof FormGroup) {
        this.markFormGroupTouched(control);
      }
    });
  }

  private handleReservationSuccess(response: any): void {
    this.reservationSuccess = true;
    this.reservationCode = response.reservation?.code_res || 'N/A';
    this.event.nbr_place = response.remaining_places;
    this.reservationComplete.emit({
      success: true,
      remainingPlaces: response.remaining_places
    });
    
    // Reset form but keep the success message
    this.reservationForm.reset({
      evenement_id: this.event.id,
      quantity: 1
    });
  }

  private handleReservationError(error: any): void {
    console.error('Erreur:', error);

    if (error.status === 401) {
      this.reservationError = 'Session expirée. Veuillez vous reconnecter.';
      this.authService.performLogout();
    } else if (error.status === 400) {
      this.reservationError = error.error?.message || 'Places insuffisantes';
    } else if (error.status === 404) {
      this.reservationError = 'Seuls les participants peuvent effectuer une réservation.';
    } else if (error.status === 422 && error.error?.errors) {
      this.reservationError = Object.values(error.error.errors).flat().join(', ');
    } else {
      this.reservationError = error.error?.message || error.message || 'Erreur lors de la réservation';
    }
  }

  redirectToLogin(): void {
    this.router.navigate(['/login']);
  }

  onCloseModal(): void {
    this.closeModal.emit();
  }
}