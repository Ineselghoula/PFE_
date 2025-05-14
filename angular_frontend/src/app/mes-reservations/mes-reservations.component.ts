import { Component, OnInit, OnDestroy } from '@angular/core';
import { EvenementService } from '../evenement.service';
import { Subject } from 'rxjs';
import { takeUntil, finalize } from 'rxjs/operators';

interface Event {
  id: number;
  titre: string;
  date_debut: string;
  image: string;
}

interface Reservation {
  id: number;
  code_res: string;
  quantity: number;
  evenement_id: number;
  participant_id: number;
  evenement: Event;
}

@Component({
  selector: 'app-mes-reservations',
  templateUrl: './mes-reservations.component.html',
  styleUrls: ['./mes-reservations.component.css']
})
export class MesReservationsComponent implements OnInit, OnDestroy {
  private destroy$ = new Subject<void>();

  // State management
  state = {
    loading: true,
    cancelling: false,
    showCancelConfirmation: false,
    selectedReservation: null as Reservation | null,
    error: null as string | null,
    successMessage: null as string | null
  };

  // Data
  reservations: Reservation[] = [];

  constructor(private evenementService: EvenementService) {}

  ngOnInit(): void {
    this.loadReservations();
  }

  ngOnDestroy(): void {
    this.destroy$.next();
    this.destroy$.complete();
  }

  // Data loading
  private loadReservations(): void {
    this.setState({ loading: true, error: null, successMessage: null });

    this.evenementService.getParticipantReservations()
      .pipe(
        takeUntil(this.destroy$),
        finalize(() => this.setState({ loading: false }))
      )
      .subscribe({
        next: (response: { data: Reservation[] }) => {
          this.reservations = response.data || [];
        },
        error: (err: any) => this.handleError(err)
      });
  }

  // UI helpers
  onImageError(event: Event & { target: HTMLImageElement }): void {
    event.target.src = '/assets/placeholder-event.jpg';
  }

  canCancelReservation(eventDate: string): boolean {
    const eventDateTime = new Date(eventDate).getTime();
    const now = new Date().getTime();
    const twentyFourHoursInMs = 24 * 60 * 60 * 1000;
    return (eventDateTime - now) > twentyFourHoursInMs;
  }

  getEventImage(reservation: Reservation): string {
    return reservation.evenement?.image
      ? `http://127.0.0.1:8000/storage/${reservation.evenement.image}`
      : '/assets/placeholder-event.jpg';
  }

  isContentReady(): boolean {
    return !this.state.loading && !this.state.error;
  }

  trackByReservationId(index: number, reservation: Reservation): number {
    return reservation.id;
  }

  // Reservation actions
  requestCancel(reservation: Reservation): void {
    this.setState({
      selectedReservation: reservation,
      showCancelConfirmation: true
    });
  }

  cancelReservation(): void {
    if (!this.state.selectedReservation || this.state.cancelling) return;

    this.setState({ cancelling: true });

    const { code_res, evenement_id, participant_id } = this.state.selectedReservation;

    this.evenementService.cancelReservation(code_res, evenement_id, participant_id)
      .pipe(
        takeUntil(this.destroy$),
        finalize(() => this.setState({
          cancelling: false,
          showCancelConfirmation: false
        }))
      )
      .subscribe({
        next: () => this.handleCancellationSuccess(),
        error: (err: any) => this.handleError(err)
      });
  }

  // State management
  private setState(partialState: Partial<typeof this.state>): void {
    this.state = { ...this.state, ...partialState };
  }

  // Public methods for template
  closeModal(): void {
    this.setState({ showCancelConfirmation: false });
  }

  dismissError(): void {
    this.setState({ error: null });
  }

  dismissSuccessMessage(): void {
    this.setState({ successMessage: null });
  }

  // Error handling
  private handleError(err: any): void {
    let errorMessage = 'Erreur inattendue.';

    if (err.error?.message) {
      errorMessage = err.error.message;
    } else if (err.status === 401) {
      errorMessage = 'Veuillez vous connecter pour voir vos réservations.';
    }

    this.setState({ error: errorMessage });
    console.error(err);
  }

  private handleCancellationSuccess(): void {
    this.setState({
      successMessage: 'Réservation annulée avec succès'
    });

    setTimeout(() => this.setState({ successMessage: null }), 3000);
    this.loadReservations();
  }

  // UI actions
  retryLoading(): void {
    this.loadReservations();
  }
}
