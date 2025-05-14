import { Component, OnInit } from '@angular/core';
import { EvenementService } from '../evenement.service'; 
import { Evenement } from '../models/evenement.model';
import { Router } from '@angular/router';

@Component({
  selector: 'app-organisateur-evenements',
  templateUrl: './organisateur-evenements.component.html',
  styleUrls: ['./organisateur-evenements.component.css']
})
export class OrganisateurEvenementsComponent implements OnInit {
  evenements: Evenement[] = [];
  filteredEvents: Evenement[] = [];
  isLoading = true;
  errorMessage = '';
  
  // Filtres
  searchQuery = '';
  statusFilter = 'all';
  sortOption = 'newest';

  constructor(
    private evenementService: EvenementService, 
    private router: Router
  ) {}

  ngOnInit(): void {
    this.fetchEvenements();
  }

  fetchEvenements(): void {
    this.isLoading = true;
    this.evenementService.getOrganisateurEvents().subscribe({
      next: (res: { evenements: Evenement[] }) => {
        this.evenements = res.evenements;
        this.applyFilters();
        this.isLoading = false;
      },
      error: (err: any) => {
        console.error('Erreur lors de la récupération des événements:', err);
        this.errorMessage = 'Une erreur est survenue lors du chargement des événements.';
        this.isLoading = false;
      }
    });
  }

  applyFilters(): void {
    let filtered = [...this.evenements];

    // Filtre par recherche
    if (this.searchQuery) {
      const query = this.searchQuery.toLowerCase();
      filtered = filtered.filter(event => 
        event.titre.toLowerCase().includes(query) || 
        event.description.toLowerCase().includes(query) ||
        event.adresse.toLowerCase().includes(query)
      );
    }

    // Filtre par statut
    if (this.statusFilter !== 'all') {
      filtered = filtered.filter(event => {
        if (this.statusFilter === 'approved') return event.approved == true;
        if (this.statusFilter === 'pending') return event.approved == false;
        return true;
      });
    }

    // Tri
    filtered.sort((a, b) => {
      switch (this.sortOption) {
        case 'newest':
          return new Date(b.date_debut).getTime() - new Date(a.date_debut).getTime();
        case 'oldest':
          return new Date(a.date_debut).getTime() - new Date(b.date_debut).getTime();
        case 'title-asc':
          return a.titre.localeCompare(b.titre);
        case 'title-desc':
          return b.titre.localeCompare(a.titre);
        default:
          return 0;
      }
    });

    this.filteredEvents = filtered;
  }

  goToUpdateEvent(eventId: number): void {
    this.router.navigate(['/update-event', eventId]);
  }

  deleteEvenement(eventId: number): void {
    const confirmation = confirm('Êtes-vous sûr de vouloir supprimer cet événement ?');
    if (!confirmation) return;
  
    this.evenementService.deleteEvenement(eventId).subscribe({
      next: () => {
        this.showAlert('success', 'Événement supprimé avec succès');
        this.fetchEvenements();
      },
      error: (err) => {
        console.error('Erreur lors de la suppression:', err);
        this.showAlert('error', 'Erreur lors de la suppression');
      }
    });
  }
  
  private showAlert(type: 'success' | 'error', message: string): void {
    const alert = document.createElement('div');
    alert.className = `custom-alert ${type}`;
    alert.textContent = message;
    
    document.body.appendChild(alert);
    
    setTimeout(() => {
      alert.style.opacity = '1';
      alert.style.transform = 'translateY(0)';
    }, 10);
    
    setTimeout(() => {
      alert.style.opacity = '0';
      setTimeout(() => {
        document.body.removeChild(alert);
      }, 300);
    }, 3000);
  }
}