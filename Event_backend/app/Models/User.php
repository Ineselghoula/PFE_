<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'role',
        'actif',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Relation avec le modèle Organisateur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function organisateur()
    {
        return $this->hasOne(Organisateur::class);
    }

    /**
     * Relation avec le modèle Participant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function participant()
    {
        return $this->hasOne(Participant::class);
    }
}