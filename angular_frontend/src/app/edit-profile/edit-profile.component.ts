import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Router } from '@angular/router';

@Component({
  selector: 'app-edit-profile',
  templateUrl: './edit-profile.component.html',
  styleUrls: ['./edit-profile.component.css']
})
export class EditProfileComponent implements OnInit {
  profileForm!: FormGroup;
  user: any;
  token = localStorage.getItem('access_token');
  imagePreview: string | ArrayBuffer | null = null;
  selectedImage: File | null = null;

  constructor(private fb: FormBuilder, private http: HttpClient, private router: Router) {}

  ngOnInit(): void {
    this.fetchUserProfile();
  }

  fetchUserProfile() {
    const headers = new HttpHeaders().set('Authorization', `Bearer ${this.token}`);
    this.http.get<any>('http://127.0.0.1:8000/api/auth/user/profile', { headers }).subscribe(
      res => {
        this.user = res.user;
        this.initForm();
        if (this.user.image) this.imagePreview = this.user.image;
      },
      err => console.error('Erreur chargement profil:', err)
    );
  }

  initForm() {
    this.profileForm = this.fb.group({
      first_name: [this.user.first_name],
      last_name: [this.user.last_name],
      phone: [this.user.phone],
      date_naissance: [this.user.date_naissance],
      adresse: [this.user.adresse],
      nom_societe: [this.user.nom_societe],
      site_web: [this.user.site_web],
      reseau_social: [this.user.reseau_social],
      biographie: [this.user.biographie],
      image: [null]
    });
  }

  onImageChange(event: any) {
    const file = event.target.files[0];
    if (file) {
      this.selectedImage = file;
      const reader = new FileReader();
      reader.onload = () => this.imagePreview = reader.result;
      reader.readAsDataURL(file);
    }
  }

  onSubmit() {
    const formData = new FormData();
    Object.keys(this.profileForm.controls).forEach(key => {
      if (this.profileForm.get(key)?.value) {
        formData.append(key, this.profileForm.get(key)?.value);
      }
    });

    if (this.selectedImage) {
      formData.append('image', this.selectedImage);
    }

    const headers = new HttpHeaders().set('Authorization', `Bearer ${this.token}`);
    this.http.post('http://127.0.0.1:8000/api/auth/update', formData, { headers }).subscribe(
      () => this.router.navigate(['/profile']),
      err => console.error('Erreur mise à jour:', err)
    );
  }

  onCancel() {
    this.router.navigate(['/profile']);
  }
}
