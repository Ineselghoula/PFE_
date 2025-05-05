import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Router } from '@angular/router';

@Component({
  selector: 'app-create-evenement',
  templateUrl: './create-event.component.html',
  styleUrls: ['./create-event.component.css']
})
export class CreateEventComponent implements OnInit {
  eventForm!: FormGroup;
  categories: any[] = [];
  sousCategories: any[] = [];
  selectedImage: File | null = null;
  errorMessages: any = {};
  successMessage: string = '';

  constructor(
    private fb: FormBuilder,
    private http: HttpClient,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.eventForm = this.fb.group({
      titre: ['', Validators.required],
      description: ['', Validators.required],
      date_debut: ['', Validators.required],
      date_fin: ['', Validators.required],
      image: [null],
      map_link: ['', Validators.required],
      prix: [0, Validators.required],
      adresse: ['', Validators.required],
      temps: ['', Validators.required],
      nbr_place: [0, Validators.required],
      sous_categorie_id: ['', Validators.required],
      etat: ['Disponible', Validators.required] // ✅ Champ ajouté ici
    });

    this.getCategories();
  }

  getCategories() {
    this.http.get<any[]>('http://localhost:8000/api/categories')
      .subscribe(data => {
        this.categories = data;
      });
  }

  onCategoryChange(event: any) {
    const selectedCategorie = event.target.value;
    this.http.get<any[]>(`http://localhost:8000/api/categories/${selectedCategorie}/sous-categories`)
      .subscribe(data => {
        this.sousCategories = data;
      });
  }

  onFileChange(event: any) {
    if (event.target.files && event.target.files.length) {
      this.selectedImage = event.target.files[0];
    }
  }

  submitForm() {
    const formData = new FormData();

    for (const key in this.eventForm.value) {
      if (this.eventForm.value.hasOwnProperty(key)) {
        formData.append(key, this.eventForm.value[key]);
      }
    }

    // Ajouter l'image si elle existe
    if (this.selectedImage) {
      formData.append('image', this.selectedImage);
    }

    // Authentification
    const token = localStorage.getItem('token');

    this.http.post('http://127.0.0.1:8000/api/auth/evenements', formData, {
      headers: new HttpHeaders({
        Authorization: `Bearer ${token}`
      })
    }).subscribe({
      next: (res: any) => {
        this.successMessage = res.message;
        this.errorMessages = {};
        this.eventForm.reset();
        this.selectedImage = null;

        setTimeout(() => {
          this.router.navigate(['/show-evenement']);
        });
      },
      error: (err) => {
        if (err.status === 400) {
          this.errorMessages = err.error.errors;
        } else {
          console.error(err);
        }
      }
    });
  }

  cancel() {
    this.router.navigate(['/show-evenement']);
    this.eventForm.reset();
    this.errorMessages = {};
  }
}
