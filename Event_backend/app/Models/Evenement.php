<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evenement extends Model
{
    use HasFactory;

    protected $table = 'evenements'; // Nom de la table associée au modèle

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
        'categorie_id',
        'organisateur_id'
    ];

    /**
     * Relation avec la catégorie (Un événement appartient à une catégorie).
     */
    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'categorie_id'); // Corrected 'Gategorie' to 'Categorie'
    }

    /**
     * Relation avec l'organisateur (Un événement appartient à un organisateur).
     */
    public function organisateur()
    {
        return $this->belongsTo(Organisateur::class, 'organisateur_id');
    }
    
    public function commentaire() {
        return $this->hasMany(Commentaire::class);
    }

    public function notifications() {
        return $this->hasMany(Notification::class);
    }
    
    public function reservations() {
        return $this->hasMany(Reservation::class);
    }
}
