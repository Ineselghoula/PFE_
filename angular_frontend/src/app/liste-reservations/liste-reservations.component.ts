import { Component, OnInit } from '@angular/core';
import { EvenementService } from '../evenement.service';

@Component({
  selector: 'app-liste-reservations',
  templateUrl: './liste-reservations.component.html',
  styleUrls: ['./liste-reservations.component.css']
})
export class ListeReservationsComponent implements OnInit {
  evenements: any[] = [];
  filteredEvents: any[] = [];
  groupedEvents: any[] = [];
  reservations: any[] = [];
  selectedEventId: number | null = null;
  selectedEventTitle: string = '';
  reservationsLoading: boolean = false;
  searchTerm: string = '';
  currentPage: number = 1;
  itemsPerPage: number = 10;

  constructor(private evenementService: EvenementService) {}

  ngOnInit(): void {
    this.loadOrganisateurEvents();
  }

  loadOrganisateurEvents(): void {
    this.evenementService.getOrganisateurEvents().subscribe({
      next: (res) => {
        this.evenements = (res.data || res.evenements || [])
          .filter((event: any) => event.approved == true)
          .sort((a: any, b: any) => 
            new Date(a.date_debut).getTime() - new Date(b.date_debut).getTime());
        
        this.filterEvents();
      },
      error: (err) => {
        console.error('Erreur chargement événements', err);
      }
    });
  }

  filterEvents(): void {
    this.filteredEvents = this.evenements.filter(event => 
      event.titre.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
      event.adresse.toLowerCase().includes(this.searchTerm.toLowerCase())
    );

    this.groupEventsByDate();
  }

  groupEventsByDate(): void {
    const groupsMap = new Map<string, any[]>();
    
    this.filteredEvents.forEach(event => {
      const dateStr = event.date_debut.split('T')[0];
      if (!groupsMap.has(dateStr)) {
        groupsMap.set(dateStr, []);
      }
      groupsMap.get(dateStr)?.push(event);
    });

    this.groupedEvents = Array.from(groupsMap.entries()).map(([date, events]) => ({
      date,
      events
    })).sort((a, b) => new Date(a.date).getTime() - new Date(b.date).getTime());
  }

  loadReservations(eventId: number): void {
    this.reservationsLoading = true;
    this.selectedEventId = eventId;
    this.currentPage = 1;
    this.reservations = [];
    
    const selectedEvent = this.evenements.find(evt => evt.id === eventId);
    this.selectedEventTitle = selectedEvent?.titre || '';

    this.evenementService.getReservationByEvent(eventId).subscribe({
      next: (res) => {
        this.reservations = res.data || [];
        this.reservationsLoading = false;
      },
      error: (err) => {
        console.error('Erreur chargement réservations', err);
        this.reservationsLoading = false;
      }
    });
  }

  get paginatedReservations(): any[] {
    const startIndex = (this.currentPage - 1) * this.itemsPerPage;
    return this.reservations.slice(startIndex, startIndex + this.itemsPerPage);
  }

  getPageNumbers(): number[] {
    const pageCount = Math.ceil(this.reservations.length / this.itemsPerPage);
    return Array.from({length: pageCount}, (_, i) => i + 1);
  }
}