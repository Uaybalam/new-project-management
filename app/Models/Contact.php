<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'company', 'position', 'address', 'notes'
    ];

    // Relaciones futuras:

    // Relación con entidades como accionista
    public function entitiesAsShareholder()
    {
        return $this->morphToMany(Entity::class, 'shareholdable', 'shareholders');
    }
    public function notes()
    {
        return $this->morphMany(Note::class, 'notable');
    }

    // Notas asociadas (relación polimórfica)
    public function shareholder()
    {
        return $this->hasMany(Shareholder::class);
    }
    public function activities()
    {
        return $this->morphMany(Activity::class, 'activitable');
    }
}
