<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commentaire extends Model
{
    use HasFactory;

    protected $table = 'commentaires'; // Nom de la table

    protected $fillable = [
        'user_id',
        'evenement_id',
        'description',
        'date_reclamation'
    ];

    public $incrementing = false; // Désactiver l'auto-incrémentation (clé composite)
    protected $primaryKey = ['user_id', 'evenement_id']; // Définition de la clé primaire composite

    /**
     * Relation avec Utilisateur (Un utilisateur peut laisser plusieurs commentaires).
     */
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relation avec Evenement (Un événement peut recevoir plusieurs commentaires).
     */
    public function evenement()
    {
        return $this->belongsTo(Evenement::class, 'evenement_id');
    }
}
