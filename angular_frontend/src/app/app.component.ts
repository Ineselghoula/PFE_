import { Component } from '@angular/core';
import { Router, NavigationEnd } from '@angular/router';


@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css'],
})
export class AppComponent {
  title = 'angular_frontend';
  showLayout: boolean = true;
  showAllEvents: boolean = true;

  constructor(private router: Router) {
    this.router.events.subscribe((event) => {
      if (event instanceof NavigationEnd) {
        this.showLayout = !(
          this.router.url.includes('/login') || 
          this.router.url.includes('/register') || 
          this.router.url.includes('/verify-email') ||
          this.router.url.includes('/profile') ||
          this.router.url.includes('/logout') ||
          this.router.url.includes('/edit-profile') ||
          this.router.url.includes('/create-event') ||
          this.router.url.includes('/show-evenement') ||
          this.router.url.includes('/update-event') ||
          this.router.url.includes('/approve-organizers') ||
          this.router.url.includes('/approve-events') ||
          this.router.url.includes('/resrve-event')||
          this.router.url.includes('/reservation-list') ||
          this.router.url.includes('/organisateur-reservations')||
          this.router.url.includes('/liste-reservations') ||
          this.router.url.includes('/mes-reservations') ||
          this.router.url.includes('/notification')||
          this.router.url.includes('/dashboard')||
          this.router.url.includes('/organizator-dashboard')||
          this.router.url.includes('/admin-dashboard')




        );
        
        if (this.router.url === '/') {
          this.showAllEvents = true;
        }
      }
    });
  }

}