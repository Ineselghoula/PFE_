<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\AdminController;
class Categorie extends Model
{
    use HasFactory;

    protected $table = 'categories'; 
    protected $fillable = [
        'type'
    ];

    public function sousCategories()
    {
        return $this->hasMany(SousCategorie::class, 'categorie_id');
    }
}
