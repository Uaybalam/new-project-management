<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'tax_id', 'type', 'phone', 'email', 'address', 'notes'];

    public function shareholders()
    {
        return $this->morphToMany(self::class, 'shareholdable', 'shareholders')
            ->using(Shareholder::class)
            ->withPivot('percentage')
            ->withTimestamps();
    }
    public function contactsShareholders(){
        return $this->morphedByMany(Contact::class, 'shareholdable', 'shareholders')
            ->withPivot('percentage')
            ->withTimestamps();
    }
    public function entitiesShareholders()
    {
        return $this->morphedByMany(Entity::class, 'shareholdable', 'shareholders')
            ->withPivot('percentage')
            ->withTimestamps();
    }
    public function notes()
    {
        return $this->morphMany(Nota::class, 'notable');
    }

    
}
