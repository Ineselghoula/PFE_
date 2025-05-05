// src/app/reservation-list/reservation-list.component.ts
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { EvenementService } from '../evenement.service';

@Component({
  selector: 'app-reservation-list',
  templateUrl: './reservation-list.component.html',
  styleUrls: ['./reservation-list.component.css']
})
export class ReservationListComponent implements OnInit {
  reservations: any[] = [];
  eventId!: number;

  constructor(
    private route: ActivatedRoute,
    private evenementService: EvenementService
  ) {}

  ngOnInit(): void {
    this.eventId = Number(this.route.snapshot.paramMap.get('id'));
    if (this.eventId) {
      this.evenementService.getReservationByEvent(this.eventId).subscribe({
        next: (res) => {
          this.reservations = res.data;
        },
        error: (err) => {
          console.error('Erreur lors du chargement des réservations', err);
        }
      });
    }
  }
}
