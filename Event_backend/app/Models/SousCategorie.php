<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SousCategorie extends Model
{
    use HasFactory;

    protected $fillable = [
        'sous_categorie',
        'categorie_id'
    ];

    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }

    public function evenements()
    {
        return $this->hasMany(Evenement::class, 'sous_categorie_id');
    }

    public static function createSousCategorie(array $data)
    {
        $sousCategorie = self::create($data);
        return $sousCategorie->id;
    }
}
