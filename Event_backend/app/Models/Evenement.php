<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Evenement extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'titre',
        'description',
        'date_debut',
        'date_fin',
        'image',
        'map_link',
        'prix',
        'adresse',
        'temps',
        'etat',
        'nbr_place',
        'organisateur_id',
        'sous_categorie_id',
        'approved',
    ];

    /**
     * Relation vers le modèle Organisateur.
     * L'organisateur est un enregistrement de la table 'organisateurs'.
     */
    public function organisateur()
    {
        return $this->belongsTo(Organisateur::class, 'organisateur_id');
    }

    /**
     * Relation vers la sous-catégorie.
     */
    public function sousCategorie()
    {
        return $this->belongsTo(SousCategorie::class, 'sous_categorie_id');
    }

    /**
     * Relation vers les réservations associées à cet événement.
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'evenement_id');
    }
}
