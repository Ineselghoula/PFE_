<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organisateur extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nom_societe',
        'site_web',
        'reseau_social',
        'biographie',
    ];

    /**
     * Relation avec l'utilisateur (Un organisateur appartient à un utilisateur).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function evenements() {
        return $this->hasMany(Evenement::class);
    }

    public function notifications() {
        return $this->hasMany(Notification::class);
    }
}


