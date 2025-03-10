<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $table = 'reservations'; // Nom de la table

    protected $fillable = [
        'participant_id',
        'evenement_id',
        'date_reservation',
        'nbr_participant'
    ];

    public $incrementing = false; // Désactiver l'auto-incrémentation (clé composite)
    protected $primaryKey = ['participant_id', 'evenement_id']; 

    /**
     * Relation avec Participant (Un participant peut avoir plusieurs réservations).
     */
    public function participant()
    {
        return $this->belongsTo(Participant::class, 'participant_id');
    }

    /**
     * Relation avec Evenement (Un événement peut avoir plusieurs réservations).
     */
    public function evenement()
    {
        return $this->belongsTo(Evenement::class, 'evenement_id');
    }
}
