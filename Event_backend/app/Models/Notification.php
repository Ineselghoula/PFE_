<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications'; // Table name

    protected $fillable = [
        'user_id',
        'evenement_id',
        'contenu',
        'type',
        'date_envoi'
    ];

    // Disable auto-incrementing for composite key
    public $incrementing = false;
    
    // Define composite primary key
    protected $primaryKey = ['user_id', 'evenement_id'];

    /**
     * Get the key name of the composite primary key
     */
    public function getKeyName()
    {
        return 'user_id'; // Override to define a composite key
    }

    /**
     * Relation with Utilisateur (User)
     */
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relation with Evenement
     */
    public function evenement()
    {
        return $this->belongsTo(Evenement::class, 'evenement_id');
    }
}
