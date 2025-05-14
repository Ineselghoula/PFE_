// src/app/approve-events/approve-events.component.ts
import { Component, OnInit } from '@angular/core';
import { EvenementService } from '../evenement.service';

@Component({
  selector: 'app-approve-event',
  templateUrl: './approve-events.component.html',
  styleUrls: ['./approve-events.component.css']
})
export class ApproveEventsComponent implements OnInit {
  events: any[] = [];
  selectedEvent: any = null;
  loading = false;
  errorMessage = '';
  successMessage = ''; // ✅ Ajout du message de succès

  constructor(private evenementService: EvenementService) {}

  ngOnInit(): void {
    this.fetchPendingEvents();
  }

  // Récupérer les événements en attente d'approbation
  fetchPendingEvents() {
    this.loading = true;
    this.evenementService.getUnapprovedEvents().subscribe({
      next: (response) => {
        console.log('Réponse API:', response);
        this.events = response.evenements ?? response ?? [];
        this.loading = false;
      },
      error: (error) => {
        console.error('Erreur lors du chargement des événements', error);
        this.loading = false;
      }
    });
  }

  // Approuver un événement
  approveEvent(eventId: number) {
    this.evenementService.approveEvenement(eventId).subscribe({
      next: (response: any) => {
        this.successMessage = response.message || 'Événement approuvé avec succès.';
        this.events = this.events.filter(event => event.id !== eventId);
        setTimeout(() => this.successMessage = '', 3000); // Disparaît après 3s
      },
      error: (error) => {
        console.error("Erreur lors de l'approbation", error);
        this.errorMessage = 'Erreur lors de l’approbation de l’événement.';
      }
    });
  }

  // Rejeter un événement
  rejectEvent(eventId: number) {
    this.evenementService.rejectEvent(eventId).subscribe({
      next: (response: any) => {
        this.successMessage = response.message || 'Événement rejeté avec succès.';
        this.events = this.events.filter(event => event.id !== eventId);
        setTimeout(() => this.successMessage = '', 3000);
      },
      error: (error) => {
        console.error("Erreur lors du rejet", error);
        this.errorMessage = 'Erreur lors du rejet de l’événement.';
      }
    });
  }

  // Convertir "HH:mm:ss" en Date
  convertTimeToDate(time: string): Date {
    const [hours, minutes, seconds] = time.split(':');
    const currentDate = new Date();
    currentDate.setHours(parseInt(hours), parseInt(minutes), parseInt(seconds || '0'), 0);
    return currentDate;
  }
}
