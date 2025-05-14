<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;


class Organisateur extends Model
{
    use HasFactory,Notifiable;

    protected $fillable = [
        'user_id',
        'nom_societe',
        'site_web',
        'reseau_social',
        'biographie',
        'is_approved'
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


    public function isApproved()
    {
        // Si l'utilisateur est un organisateur, vérifiez s'il est approuvé
        if ($this->role === 'organisateur' && $this->organisateur) {
            return $this->organisateur->is_approved;
        }
    
        // Pour les autres rôles (admin, participant), considérez qu'ils sont automatiquement approuvés
        return true;
    }
}



