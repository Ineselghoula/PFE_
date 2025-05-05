export class Evenement {
    id: number;
    titre: string;
    description: string;
    date_debut: string;
    date_fin: string;
    image?: string;
    map_link: string;
    prix: number;
    adresse: string;
    temps: string;
    etat: string;
    nbr_place: number;
    sous_categorie_id: number;
    organisateur_id: number;
    approved: boolean;
  
    constructor(
      id: number,
      titre: string,
      description: string,
      date_debut: string,
      date_fin: string,
      map_link: string,
      prix: number,
      adresse: string,
      temps: string,
      etat: string,
      nbr_place: number,
      sous_categorie_id: number,
      organisateur_id: number,
      approved: boolean,
      image?: string
    ) {
      this.id = id;
      this.titre = titre;
      this.description = description;
      this.date_debut = date_debut;
      this.date_fin = date_fin;
      this.map_link = map_link;
      this.prix = prix;
      this.adresse = adresse;
      this.temps = temps;
      this.etat = etat;
      this.nbr_place = nbr_place;
      this.sous_categorie_id = sous_categorie_id;
      this.organisateur_id = organisateur_id;
      this.approved = approved;
      this.image = image;
    }
  }
  