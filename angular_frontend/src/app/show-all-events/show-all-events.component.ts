import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { EvenementService } from '../evenement.service';
import { AuthService } from '../auth.service';

@Component({
  selector: 'app-show-all-events',
  templateUrl: './show-all-events.component.html',
  styleUrls: ['./show-all-events.component.css']
})
export class ShowAllEventsComponent implements OnInit {
  groupedEvents: {date: string, events: any[]}[] = [];
  selectedEvent: any = null;
  errorMessage = '';
  searchMode = false;
  isAuthenticated = false;
  isLoading = false;
  today = new Date();

  constructor(
    private evenementService: EvenementService,
    private authService: AuthService,
    private router: Router
  ) {
    this.today.setHours(0, 0, 0, 0); // Normaliser la date du jour
  }

  ngOnInit(): void {
    this.loadAllEvents();
  }


  loadAllEvents(): void {
    this.isLoading = true;
    this.evenementService.getPublicEvents().subscribe({
      next: (res) => {
        this.groupEventsByDate(res.evenements || []);
        this.errorMessage = '';
        this.isLoading = false;
      },
      error: (err) => {
        this.isLoading = false;
        this.errorMessage = err.error?.message || 'Erreur de chargement des événements.';
        if (err.status === 401) this.handleUnauthorized();
      }
    });
  }

  groupEventsByDate(events: any[]): void {
    const filteredEvents = events.filter(event => {
      const endDate = new Date(event.date_fin);
      endDate.setHours(23, 59, 59, 999); // Fin de la journée
      return endDate >= this.today;
    });

    // Trier par date de début (plus proche en premier)
    const sortedEvents = [...filteredEvents].sort((a, b) => {
      const dateA = new Date(a.date_debut).getTime();
      const dateB = new Date(b.date_debut).getTime();
      return dateA - dateB;
    });

    // Grouper par date
    const groupsMap = new Map<string, any[]>();
    
    sortedEvents.forEach(event => {
      const dateStr = this.formatDateRange(event.date_debut, event.date_fin);
      if (!groupsMap.has(dateStr)) {
        groupsMap.set(dateStr, []);
      }
      groupsMap.get(dateStr)?.push(event);
    });

    // Convertir en tableau pour le template
    this.groupedEvents = Array.from(groupsMap.entries()).map(([date, events]) => ({
      date,
      events
    }));
  }

  formatDateRange(start: string, end: string): string {
    const startDate = new Date(start);
    const endDate = new Date(end);
    
    // Si même jour
    if (startDate.toDateString() === endDate.toDateString()) {
      return startDate.toLocaleDateString('fr-FR', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
      });
    }
    
    // Si différent jour
    return `Du ${startDate.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long' })} 
            au ${endDate.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' })}`;
  }

  isEventToday(event: any): boolean {
    const eventDate = new Date(event.date_debut);
    eventDate.setHours(0, 0, 0, 0);
    return eventDate.getTime() === this.today.getTime();
  }

  handleUnauthorized(): void {
    this.authService.performLogout();
    this.router.navigate(['/login'], {
      queryParams: { returnUrl: this.router.url }
    });
  }

  handleSearchResults(results: any[]): void {
    this.searchMode = true;
    this.groupEventsByDate(results);
    this.errorMessage = results.length === 0 ? "Aucun événement trouvé." : '';
  }

  showEventDetails(event: any): void {
    if (!this.isAuthenticated) {
      this.redirectToLogin();
      return;
    }
    this.selectedEvent = event;
  }

  handleReservationComplete(result: {success: boolean, remainingPlaces: number}): void {
    if (result.success && this.selectedEvent) {
      this.selectedEvent.nbr_place = result.remainingPlaces;
      this.loadAllEvents();
    }
  }

  closeModal(): void {
    this.selectedEvent = null;
  }

  redirectToLogin(): void {
    this.closeModal();
    this.router.navigate(['/login'], {
      queryParams: { returnUrl: this.router.url }
    });
  }
}