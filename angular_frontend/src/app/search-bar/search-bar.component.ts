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

  searchForm!: FormGroup;
  categories: any[] = [];
  sousCategories: any[] = [];
  showEmptySearchWarning: boolean = false;

  constructor(private fb: FormBuilder, private http: HttpClient) {}

  ngOnInit(): void {
    this.searchForm = this.fb.group({
      titre: [''],
      date_debut: [''],
      date_fin: [''],
      categorie_id: [''],
      sous_categorie_id: ['']
    });

    this.http.get<any[]>('http://localhost:8000/api/categories')
      .subscribe(data => this.categories = data);
  }

  onCategoryChange(event: any): void {
    const categoryId = event.target.value;
    if (categoryId) {
      this.http.get<any[]>(`http://localhost:8000/api/categories/${categoryId}/sous-categories`)
        .subscribe(data => this.sousCategories = data);
    } else {
      this.sousCategories = [];
    }
  }

  onSearch(): void {
    const formValues = this.searchForm.value;
    const allFieldsEmpty = Object.values(formValues).every(value => !value || value === '');

    if (allFieldsEmpty) {
      this.showEmptySearchWarning = true;
      this.searchResults.emit([]); // Aucun résultat si vide
      return;
    }

    this.showEmptySearchWarning = false;

    let params = new HttpParams();
    Object.keys(formValues).forEach(key => {
      if (formValues[key]) {
        params = params.set(key, formValues[key]);
      }
    });

    this.http.get<any[]>('http://localhost:8000/api/evenements-search', { params })
      .subscribe({
        next: data => this.searchResults.emit(data),
        error: err => console.error('Erreur lors de la recherche', err)
      });
  }
}
