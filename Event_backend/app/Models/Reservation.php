<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $table = 'reservations';

    protected $fillable = [
        'participant_id',
        'evenement_id',
        'full_name',
        'numero_telephone',
        'email',
        'quantity',
        'code_res',
    ];

    /**
     * Relation avec Participant (Un participant peut avoir plusieurs réservations).
     */
    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    /**
     * Relation avec Evenement (Un événement peut avoir plusieurs réservations).
     */
    public function evenement()
    {
        return $this->belongsTo(Evenement::class);
    }
}
