import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface Notification{
  id: number;
  name_evenement: string;
  type: string;
  envoye_le: string;
  contenu: string;
  user_id: number;
  evenement_id: number | null;
  read_at?: string;
}

@Injectable({
  providedIn: 'root'
})
export class NotificationService {
  private baseUrl: string = 'http://localhost:8000/api';

  constructor(private http: HttpClient) { }

  // Get all notifications
  getNotifications(): Observable<Notification[]> {
    return this.http.get<Notification[]>(`${this.baseUrl}/auth/user/notifications`);
  }

  // Mark notification as read
  markAsRead(notificationId: number): Observable<Notification> {
    return this.http.put<Notification>(`${this.baseUrl}/auth/notifications/${notificationId}/read`, {});
  }

  // Get single notification details
 getNotificationDetails(notificationId: number): Observable<Notification> {
  return this.http.get<Notification>(`http://localhost:8000/api/auth/notifications/{notificationId}`);
}

}