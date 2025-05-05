import { Evenement } from './evenement.model';

describe('Evenement', () => {
  it('should create an instance', () => {
    const evenement = new Evenement(
      1,                        // id
      'Event Name',             // titre
      'Description',            // description
      new Date().toISOString(),// date_debut
      new Date().toISOString(),// date_fin
      'https://maps.link',      // map_link
      100,                     // prix
      '123 Rue Exemple',       // adresse
      '18:00',                 // temps
      'à venir',               // etat
      200,                     // nbr_place
      5,                       // sous_categorie_id
      2,                       // organisateur_id
      true,                    // approved
      'image.jpg'              // image (optionnel)
    );
    expect(evenement).toBeTruthy();
  });
});
