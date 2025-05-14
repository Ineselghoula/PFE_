<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Reservation extends Model
{
    use HasFactory, Notifiable;

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
     * Relation avec le participant (utilisateur ayant fait la réservation).
     */
    public function participant()
    {
        return $this->belongsTo(User::class, 'participant_id');
    }

    /**
     * Alias pour accéder au participant sous le nom "user"
     * (nécessaire si tu utilises $reservation->user ailleurs).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'participant_id');
    }

    /**
     * Relation avec l'événement réservé.
     */
    public function evenement()
    {
        return $this->belongsTo(Evenement::class);
    }
}
