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
  isLoading = true;
  errorMessage = '';

  constructor(private evenementService: EvenementService, private router: Router ) {}

  ngOnInit(): void {
    console.log ('test')
    this.fetchEvenements();
  }
  goToUpdateEvent(eventId: number) {
    this.router.navigate(['/update-event', eventId]);
  }

  fetchEvenements(): void {
    this.evenementService.getOrganisateurEvents().subscribe({
      next: (res: { evenements: Evenement[] }) => {
        this.evenements = res.evenements;
        this.isLoading = false;
      },
      error: (err: any) => {
        console.error('Erreur lors de la récupération des événements:', err);
        this.errorMessage = 'Une erreur est survenue.';
        this.isLoading = false;
      }
    });
  }
}
