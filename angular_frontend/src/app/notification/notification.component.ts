import { Component, OnInit } from '@angular/core';
import { NotificationService } from '../notification.service';
import { Notification } from '../notification.service';

@Component({
  selector: 'app-notification',
  templateUrl: './notification.component.html',
  styleUrls: ['./notification.component.css']
})
export class NotificationComponent implements OnInit {
  notifications: Notification[] = [];
  filteredNotifications: Notification[] = [];
  selectedNotification: Notification | null = null;
  showDetails = false;
  currentFilter: string = 'all';

  constructor(private notificationService: NotificationService) { }

  ngOnInit(): void {
    this.loadNotifications();
  }

  loadNotifications(): void {
    this.notificationService.getNotifications().subscribe(
      (data) => {
        this.notifications = data;
        this.applyFilter();
      },
      (error) => {
        console.error('Error loading notifications:', error);
      }
    );
  }

  applyFilter(): void {
    switch(this.currentFilter) {
      case 'unread':
        this.filteredNotifications = this.notifications.filter(n => !n.read_at);
        break;
      case 'events':
        this.filteredNotifications = this.notifications.filter(n => n.type === 'event');
        break;
      case 'alerts':
        this.filteredNotifications = this.notifications.filter(n => n.type === 'alert');
        break;
      case 'system':
        this.filteredNotifications = this.notifications.filter(n => n.type === 'system');
        break;
      default:
        this.filteredNotifications = [...this.notifications];
    }
  }

  viewNotificationDetails(notification: Notification): void {
    if (!notification.read_at) {
      this.notificationService.markAsRead(notification.id).subscribe(() => {
        notification.read_at = new Date().toISOString();
        this.applyFilter(); // Re-apply filter in case we're showing only unread
      });
    }
    
    this.selectedNotification = notification;
    this.showDetails = true;
  }

  

  closeDetails(): void {
    this.showDetails = false;
    this.selectedNotification = null;
  }

  getNotificationIcon(type: string): string {
    switch(type) {
      case 'event': return 'fas fa-calendar-alt';
      case 'alert': return 'fas fa-exclamation-circle';
      case 'system': return 'fas fa-cog';
      default: return 'fas fa-bell';
    }
  }

  getNotificationTypeLabel(type: string): string {
    switch(type) {
      case 'event': return 'Événement';
      case 'alert': return 'Alerte';
      case 'system': return 'Système';
      default: return 'Notification';
    }
  }
}