import { Component } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
@Component({
  selector: 'app-search-bar',
  templateUrl: './search-bar.component.html',
  styleUrls: ['./search-bar.component.css'],
})
export class SearchBarComponent {
  searchQuery: string = '';
  selectedCategory: string = '';
  selectedRegion: string = '';
  selectedDate: string = '';
  events: any[] = [];
  regions = [
    { value: 'tunis', label: 'Tunis' },
    { value: 'sfax', label: 'Sfax' },
    { value: 'sousse', label: 'Sousse' },
    { value: 'kairouan', label: 'Kairouan' },
    { value: 'bizerte', label: 'Bizerte' },
    { value: 'gabes', label: 'Gabès' },
    { value: 'ariana', label: 'Ariana' },
    { value: 'gafsa', label: 'Gafsa' },
    { value: 'monastir', label: 'Monastir' },
    { value: 'nabeul', label: 'Nabeul' },
    { value: 'beja', label: 'Béja' },
    { value: 'kef', label: 'Le Kef' },
    { value: 'mahdia', label: 'Mahdia' },
    { value: 'medenine', label: 'Médenine' },
    { value: 'tataouine', label: 'Tataouine' },
    { value: 'tozeur', label: 'Tozeur' },
    { value: 'zaghouan', label: 'Zaghouan' },
    { value: 'manouba', label: 'La Manouba' },
    { value: 'kebili', label: 'Kébili' },
    { value: 'sidibouzid', label: 'Sidi Bouzid' },
    { value: 'siliana', label: 'Siliana' },
    { value: 'jendouba', label: 'Jendouba' },
    { value: 'benarous', label: 'Ben Arous' },
  ];

  constructor(private http: HttpClient) {}

  onSearch() {
    // Créer les paramètres de la requête
    const params = new HttpParams()
      .set('category', this.selectedCategory)
      .set('region', this.selectedRegion)
      .set('date', this.selectedDate);

    // Afficher les paramètres dans la console pour le débogage
    console.log('Paramètres de la requête :', params.toString());

    // Faire la requête HTTP
    this.http
      .get('http://localhost:8000/api/evenements/search', { params })
      .subscribe(
        (response: any) => {
          this.events = response;
          console.log('Résultats de la recherche :', this.events);
        },
        (error) => {
          console.error('Erreur lors de la recherche', error);
          console.error('Statut de l\'erreur :', error.status);
          console.error('Message d\'erreur :', error.error.message || error.message);
          console.error('Détails complets :', error);
        }
      );
  }
}