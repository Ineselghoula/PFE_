<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;


class Participant extends Model
{
    use HasFactory,Notifiable;
    protected $fillable = [
        'user_id',
        'date_naissance',
        'adresse',
    ];
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function reservations() {
        return $this->hasMany(Reservation::class);
    }

    public function commentaires() {
        return $this->hasMany(Commentaire::class);
    }
    public function notifications() {
        return $this->hasMany(Notification::class);
    }
   
}
