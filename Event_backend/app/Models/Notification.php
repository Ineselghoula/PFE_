<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'evenement_id',
        'name_evenement',
        'type',
        'envoye_le',
        'contenu',
    ];

    /**
     * Relation avec User (Une notification appartient à un utilisateur).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec Evenement (Une notification appartient à un événement).
     */
    public function evenement()
    {
        return $this->belongsTo(Evenement::class);
    }
}
