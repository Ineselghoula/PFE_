import { Component, OnInit } from '@angular/core';
import { AuthService } from '../auth.service';
import { NotificationService } from '../notification.service';

@Component({
  selector: 'app-navbar',
  templateUrl: './navbar.component.html',
  styleUrls: ['./navbar.component.css']
})
export class NavbarComponent implements OnInit {
  isLoggedIn: boolean = false;
  isSidebarOpen = false;
  unreadCount = 0;

  constructor(
    private authService: AuthService,
    private notificationService: NotificationService
  ) {}

  ngOnInit(): void {
    this.authService.isAuthenticated$.subscribe(status => {
      this.isLoggedIn = status;
      if (status) {
        this.loadUnreadCount();
      }
    });
  }

  loadUnreadCount(): void {
   
  }

  onLogout(): void {
    this.authService.logout();
  }

  toggleSidebar() {
    this.isSidebarOpen = !this.isSidebarOpen;
  }
}