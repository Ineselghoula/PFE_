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

  constructor(private router: Router) {
    this.router.events.subscribe((event) => {
      if (event instanceof NavigationEnd) {
        this.showLayout = !(
          this.router.url.includes('/login') || 
          this.router.url.includes('/register') || 
          this.router.url.includes('/verify-email') ||
          this.router.url.includes('/profile')  

        );
      }
    });
  }
}
