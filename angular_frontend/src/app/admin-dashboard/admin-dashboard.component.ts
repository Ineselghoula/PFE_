import { Component, OnInit } from '@angular/core';
import { AdminService } from '../admin.service';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';

@Component({
  selector: 'app-admin-dashboard',
  templateUrl: './admin-dashboard.component.html',
  styleUrls: ['./admin-dashboard.component.css']
})
export class AdminDashboardComponent implements OnInit {
  stats: any;
  users: any[] = [];
  recentEvents: any[] = [];
  eventsToApprove: any[] = [];
  recentReservations: any[] = [];
  activeTab: string = 'dashboard';
  loading = true;
  error: string | null = null;

  // Monthly stats data
  monthlyStats: any = {
    months: [],
    events: [],
    users: [],
    reservations: []
  };

  constructor(private adminService: AdminService) {}

  ngOnInit(): void {
    this.loadInitialData();
  }

  loadInitialData(): void {
    this.loading = true;
    this.error = null;

    this.adminService.getDashboardStats().subscribe({
      next: (response) => {
        this.stats = response.stats;
        this.recentEvents = response.recent_events;
        this.eventsToApprove = response.events_to_approve;
        this.recentReservations = response.recent_reservations || [];
        this.loading = false;
      },
      error: (err) => {
        console.error(err);
        this.error = 'Erreur lors du chargement des données';
        this.loading = false;
      }
    });

    // Charger les stats mensuelles
    this.loadMonthlyStats();
  }

  loadMonthlyStats(): void {
    this.adminService.getMonthlyStats().subscribe({
      next: (response) => {
        this.monthlyStats = response;
      },
      error: (err) => {
        console.error('Erreur stats mensuelles:', err);
      }
    });
  }

  loadUsers(): void {
    this.loading = true;
    this.error = null;

    this.adminService.getAllUsers().subscribe({
      next: (response) => {
        this.users = response.data || [];
        this.loading = false;
      },
      error: (err) => {
        console.error(err);
        this.error = 'Erreur lors du chargement des utilisateurs';
        this.loading = false;
      }
    });
  }

  approveEvent(eventId: number): void {
    if (confirm('Êtes-vous sûr de vouloir approuver cet événement ?')) {
      this.adminService.approveEvent(eventId).subscribe({
        next: () => {
          this.loadInitialData();
        },
        error: (err) => {
          console.error(err);
          alert('Erreur lors de l\'approbation');
        }
      });
    }
  }

  switchTab(tab: string): void {
    this.activeTab = tab;
    if (tab === 'users' && this.users.length === 0) {
      this.loadUsers();
    }
  }

  formatDate(dateString: string): string {
    const options: Intl.DateTimeFormatOptions = { 
      year: 'numeric', 
      month: 'short', 
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    };
    return new Date(dateString).toLocaleDateString('fr-FR', options);
  }

  // Calculer le pourcentage pour la barre de progression
  getPercentage(current: number, max: number): number {
    return max > 0 ? Math.min(100, (current / max) * 100) : 0;
  }
  getMax(values: number[]): number {
  return values && values.length > 0 ? Math.max(...values) : 1;
}


}