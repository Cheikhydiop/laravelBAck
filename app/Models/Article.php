<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = ['libelle', 'quantite'];

    public function scopeDisponible($query)
    {
        return $query->where('quantite', '>', 0);
    }

    public function scopeNonDisponible($query)
    {
        return $query->where('quantite', '=', 0);
    }
}
