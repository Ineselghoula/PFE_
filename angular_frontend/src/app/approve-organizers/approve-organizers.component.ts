import { Component, OnInit } from '@angular/core';
import { EvenementService } from '../evenement.service';

@Component({
  selector: 'app-approve-organizers',
  templateUrl: './approve-organizers.component.html',
  styleUrls: ['./approve-organizers.component.css']
})
export class ApproveOrganizersComponent implements OnInit {
  message: string = '';
  loading: boolean = false;
  unapprovedOrganizers: any[] = [];

  constructor(
    private evenementService: EvenementService
  ) {}

  ngOnInit(): void {
    this.loadUnapprovedOrganizers();
  }

  loadUnapprovedOrganizers(): void {
    this.loading = true;
    this.evenementService.getUnapprovedOrganizers().subscribe({
      next: (response: any) => {
        this.loading = false;
        this.unapprovedOrganizers = response.organizers || [];
      },
      error: (error) => {
        this.loading = false;
        this.message = error.error.message || 'Erreur lors du chargement des organisateurs';
      }
    });
  }

  approveOrganizer(userId: string): void {
    this.loading = true;
    this.evenementService.approveOrganizer(userId).subscribe({
      next: (response: any) => {
        this.loading = false;
        this.message = response.message;
        this.loadUnapprovedOrganizers();
      },
      error: (error) => {
        this.loading = false;
        this.message = error.error.message || 'Erreur lors de l\'approbation';
      }
    });
  }

  rejectOrganizer(userId: string): void {
    if (confirm('Êtes-vous sûr de vouloir refuser cet organisateur ?')) {
      this.loading = true;
      this.evenementService.rejectOrganizer(userId).subscribe({
        next: (response: any) => {
          this.loading = false;
          this.message = response.message;
          this.loadUnapprovedOrganizers();
        },
        error: (error) => {
          this.loading = false;
          this.message = error.error.message || 'Erreur lors du refus';
        }
      });
    }
  }
}