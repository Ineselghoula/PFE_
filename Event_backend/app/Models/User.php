<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'phone', 'role', 
        'image', 'email_verified', 'actif', 'verification_code', 
        'code_expires_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];



    public function organisateur()
    {
        return $this->hasOne(Organisateur::class);
    }

    /**
     * Relation avec le modèle Participant.
     */
    public function participant()
    {
        return $this->hasOne(Participant::class);
    }

    public function admin()
    {
        return $this->hasOne(Admin::class); 
    }

   
}