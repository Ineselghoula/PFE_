import { Component, OnInit, Output, EventEmitter } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { HttpClient, HttpParams } from '@angular/common/http';

@Component({
  selector: 'app-search-bar',
  templateUrl: './search-bar.component.html',
  styleUrls: ['./search-bar.component.css']
})
export class SearchBarComponent implements OnInit {
  @Output() searchResults = new EventEmitter<any[]>();
  @Output() searchInitiated = new EventEmitter<boolean>();

  searchForm!: FormGroup;
  categories: any[] = [];
  sousCategories: any[] = [];
  showEmptySearchWarning: boolean = false;
  isLoading: boolean = false;

  constructor(private fb: FormBuilder, private http: HttpClient) {}

  ngOnInit(): void {
    this.initializeForm();
    this.loadCategories();
  }

  initializeForm(): void {
    this.searchForm = this.fb.group({
      titre: [''],
      date_debut: [''],
      date_fin: [''],
      categorie_id: [''],
      sous_categorie_id: [''],
      prix_min: [''],
      prix_max: ['']
    });
  }

  loadCategories(): void {
    this.http.get<any[]>('http://localhost:8000/api/categories')
      .subscribe({
        next: data => this.categories = data,
        error: err => console.error('Erreur de chargement des catégories', err)
      });
  }

  onCategoryChange(event: any): void {
    const categoryId = event.target.value;
    this.searchForm.get('sous_categorie_id')?.setValue('');
    
    if (categoryId) {
     this.http.get<any[]>(`http://localhost:8000/api/categories/${categoryId}/sous-categories`)
  .subscribe({
    next: data => this.sousCategories = data,
    error: err => console.error('Erreur de chargement des sous-catégories', err)
  });
    } else {
      this.sousCategories = [];
    }
  }

  onSearch(): void {
    const formValues = this.searchForm.value;
    const allFieldsEmpty = Object.values(formValues).every(value => !value || value === '');

    if (allFieldsEmpty) {
      this.showEmptySearchWarning = true;
      this.searchResults.emit([]);
      this.searchInitiated.emit(false);
      return;
    }

    this.showEmptySearchWarning = false;
    this.isLoading = true;
    this.searchInitiated.emit(true);

    let params = new HttpParams();
    Object.keys(formValues).forEach(key => {
      if (formValues[key]) {
        params = params.set(key, formValues[key]);
      }
    });

    this.http.get<any[]>('http://localhost:8000/api/evenements-search', { params })
      .subscribe({
        next: data => {
          this.searchResults.emit(data);
          this.isLoading = false;
        },
        error: err => {
          console.error('Erreur lors de la recherche', err);
          this.isLoading = false;
        }
      });
  }

  
}