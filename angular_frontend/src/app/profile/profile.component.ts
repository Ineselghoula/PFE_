import { Component, OnInit } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';

interface UserProfile {
  id: number;
  first_name: string;
  last_name: string;
  email: string;
  phone?: string;
  role: string;
  image?: string | null;
  email_verified: boolean;
  actif: boolean;
  date_naissance?: string;
  adresse?: string;
  // Organisateur
  nom_societe?: string;
  site_web?: string;
  reseau_social?: string;
  biographie?: string;
  is_approved?: boolean;
  // Admin
  admin_since?: string;
}

@Component({
  selector: 'app-profile',
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.css']
})
export class ProfileComponent implements OnInit {
  user: UserProfile | null = null;
  apiUrl = 'http://127.0.0.1:8000/api/auth/user/profile';
  token = localStorage.getItem('access_token'); 
  constructor(private http: HttpClient) {}

  ngOnInit(): void {
    this.getUserProfile();
  }

  getUserProfile() {
    if (!this.token) {
      console.error('Token non trouvé !');
      return;
    }

    const headers = new HttpHeaders().set('Authorization', `Bearer ${this.token}`);

    this.http.get<{ message: string; user: UserProfile }>(this.apiUrl, { headers })
      .subscribe(
        response => {
          this.user = response.user;
        },
        error => {
          console.error('Erreur lors de la récupération du profil :', error);
        }
      );
  }
}
