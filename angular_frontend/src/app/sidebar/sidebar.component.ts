import { Component, Input, OnInit } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Router} from '@angular/router';
import { Location } from '@angular/common';

@Component({
  selector: 'app-sidebar',
  templateUrl: './sidebar.component.html',
  styleUrls: ['./sidebar.component.css']
})
export class SidebarComponent implements OnInit {
  @Input() isOpen: boolean = false;
  role: string | null = null;
  token = localStorage.getItem('access_token');

  constructor(private http: HttpClient, private router:Router, private location:Location) {}

  ngOnInit(): void {
    this.fetchUserProfile();
  }
  redirect(url: string) {
    this.location.replaceState('/'); 
    this.router.navigate([url]);
  }

  fetchUserProfile() {
    const headers = new HttpHeaders().set('Authorization', `Bearer ${this.token}`);
    this.http.get<any>('http://127.0.0.1:8000/api/auth/user/profile', { headers }).subscribe(
      res => {
        this.role = res.user.role;
        console.log('Rôle utilisateur:', this.role);
      },
      err => console.error('Erreur lors de la récupération du profil :', err)
    );
  }

  closeSidebar() {
    this.isOpen = false;
  }
}
