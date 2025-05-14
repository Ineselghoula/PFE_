import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { HttpClient } from '@angular/common/http';

@Component({
  selector: 'app-update-evenement',
  templateUrl: './update-event.component.html',
  styleUrls: ['./update-event.component.css']
})
export class UpdateEventComponent implements OnInit {
  eventForm!: FormGroup;
  evenementId!: number;
  selectedImage: File | null = null;
  imagePreview: string | null = null;
  errorMessages: string[] = [];
  successMessage = '';
  categories: any[] = [];
  sousCategories: any[] = [];
  evenementData: any;

  constructor(
    private fb: FormBuilder,
    private http: HttpClient,
    private router: Router,
    private activatedRoute: ActivatedRoute
  ) {}

  ngOnInit(): void {
    this.evenementId = Number(this.activatedRoute.snapshot.paramMap.get('id'));

    this.eventForm = this.fb.group({
      titre: [''],
      description: [''],
      date_debut: [''],
      date_fin: [''],
      map_link: [''],
      prix: [null],
      adresse: [''],
      temps: [''],
      nbr_place: [null],
      sous_categorie_id: [''],
    });

    this.getCategories();
    this.getEvenementData();
  }

  getEvenementData() {
    this.http.get<any>(`http://localhost:8000/api/evenements/${this.evenementId}`).subscribe(data => {
      this.evenementData = data;
      this.setFormData();

      if (data.sous_categorie_id && data.sous_categorie?.categorie_id) {
        this.loadSousCategoriesByCategorie(data.sous_categorie.categorie_id);
      }

      this.imagePreview = data.image || null;
    });
  }

  setFormData() {
    this.eventForm.patchValue({
      titre: this.evenementData.titre,
      description: this.evenementData.description,
      date_debut: this.evenementData.date_debut,
      date_fin: this.evenementData.date_fin,
      map_link: this.evenementData.map_link,
      prix: this.evenementData.prix,
      adresse: this.evenementData.adresse,
      temps: this.evenementData.temps,
      nbr_place: this.evenementData.nbr_place,
      sous_categorie_id: this.evenementData.sous_categorie_id,
    });
  }

  onImageChange(event: any) {
    const file = event.target.files[0];
    if (file) {
      this.selectedImage = file;
      const reader = new FileReader();
      reader.onload = () => {
        this.imagePreview = reader.result as string;
      };
      reader.readAsDataURL(file);
    }
  }

  previewImage(): string | null {
    return this.imagePreview;
  }

  getCategories() {
    this.http.get<any[]>('http://localhost:8000/api/categories').subscribe(data => {
      this.categories = data;
    });
  }

  onCategoryChange(event: any) {
    const categoryId = event.target.value;
    this.loadSousCategoriesByCategorie(categoryId);
    this.eventForm.patchValue({ sous_categorie_id: '' });
  }

  loadSousCategoriesByCategorie(categorieId: number) {
    this.http.get<any[]>(`http://localhost:8000/api/categories/${categorieId}/sous-categories`).subscribe(data => {
      this.sousCategories = data;
    });
  }

  submitForm() {
    if (this.eventForm.invalid) return;

    const formData = new FormData();
    Object.keys(this.eventForm.controls).forEach(key => {
      const value = this.eventForm.get(key)?.value;
      formData.append(key, value ?? '');
    });

    if (this.selectedImage) {
      formData.append('image', this.selectedImage);
    }

    this.http.post(`http://localhost:8000/api/auth/evenements/${this.evenementId}`, formData).subscribe({
      next: (res: any) => {
        this.successMessage = res.message || 'Événement mis à jour avec succès.';
        this.errorMessages = [];

        // Optionnel : délai avant redirection
        setTimeout(() => {
          this.router.navigate(['/show-']);
        }, );
      },
      error: (err) => {
        if (err.status === 400 && err.error.errors) {
          this.errorMessages = (Object.values(err.error.errors).flat() as unknown[]) as string[];
        } else {
          this.errorMessages = ['Une erreur est survenue. Veuillez réessayer.'];
          console.error(err);
        }
      }
    });
  }

  cancel() {
    this.router.navigate(['/show-evenement']);
    this.eventForm.reset();
    this.errorMessages = [];
  }
}
