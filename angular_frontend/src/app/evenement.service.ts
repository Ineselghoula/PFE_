// src/app/services/evenement.service.ts
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class EvenementService {
  private apiUrl = 'http://127.0.0.1:8000/api'; 
  private apiOrganisateur = 'http://localhost:8000/api/organisateur/evenements';
  private sousCategorieUrl = 'http://localhost:8000/api/sous-categories';
  private evenementBaseUrl = 'http://localhost:8000/api/evenements';

  constructor(private http: HttpClient) {}

  // Récupérer les événements d’un organisateur
  getOrganisateurEvents(): Observable<any> {
    return this.http.get<any>(this.apiOrganisateur);
  }

  //  Supprimer un événement
  deleteEvent(id: number): Observable<any> {
    return this.http.delete(`${this.evenementBaseUrl}/${id}`);
  }

  //  Récupérer toutes les sous-catégories
  getAllSousCategories(): Observable<any> {
    return this.http.get(this.sousCategorieUrl);
  }

  //  Récupérer un événement par ID
  getEvenementById(id: number): Observable<any> {
    return this.http.get(`${this.evenementBaseUrl}/${id}`);
  }

  //  Mettre à jour un événement
  updateEvenement(id: number, data: FormData): Observable<any> {
    return this.http.post(`${this.evenementBaseUrl}/${id}`, data);
  }
getPublicEvents(): Observable<any> {
  return this.http.get(`${this.apiUrl}/evenements-public`);
}

approveOrganizer(userId: string): Observable<any> {
  return this.http.post(`${this.apiUrl}/auth/approve-organizer`, { user_id: userId });
}

getUnapprovedOrganizers() {
  return this.http.get('http://localhost:8000/api/unapproved-organizers');
}
rejectOrganizer(userId: string) {
  return this.http.delete(`http://localhost:8000/api/organizers/${userId}/reject`); 
}
getUnapprovedEvents(): Observable<any> {
  return this.http.get(`http://localhost:8000/api/events-unapproved`);
}
approveEvenement(eventId: number) {
  return this.http.put(`http://localhost:8000/api/auth/evenements/${eventId}/approve`, {});
}

rejectEvent(eventId: number): Observable<any> {
  return this.http.delete(`http://localhost:8000/api/auth/evenements/${eventId}/reject`);
}

reserverEvenement(data: any) {
  return this.http.post(`http://127.0.0.1:8000/api/reserve-event`, data);
}


getOrganisateurReservations(): Observable<any> {
  return this.http.get(`http://127.0.0.1:8000/api/auth/organisateur/reservations`);
}


getReservationByEvent(evenementId: number): Observable<any> {
  return this.http.get(`http://127.0.0.1:8000/api/auth/organisateur/evenements/${evenementId}/reservations`);
}

getParticipantReservations(): Observable<any> {
  return this.http.get(`http://127.0.0.1:8000/api/auth/mes-reservations`);
}


cancelReservation(codeRes: string, eventId: number, participantId: number): Observable<any> {
  return this.http.delete(`${this.apiUrl}/reservation/annuler`, {
    params: {
      code_res: codeRes,
      evenement_id: eventId.toString(),
      participant_id: participantId.toString()
    }
  });
}
}
