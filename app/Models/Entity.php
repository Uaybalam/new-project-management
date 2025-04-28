<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    use HasFactory;

    protected $fillable = [ 'name', 'phone', 'email', 'notes',
    'entity_id', 'entity_status', 'website', 'billing_address', 'business_address',
    'document_folder_link', 'incorporation_date', 'formally_known_as', 'doing_business_as',
    'effective_entity_type_date', 'state_of_registration', 'industry', 'number_of_employees',
    'revenue_range', 'assigned_am_id', 'assigned_tm_id', 'assigned_sa_id'];

    /*public function shareholders()
    {
        return $this->morphToMany(self::class, 'shareholdable', 'shareholders')
            ->using(Shareholder::class)
            ->withPivot('percentage')
            ->withTimestamps();
    }
            */
    public function shareholders(){
        return $this->morphedByMany(Contact::class, 'shareholdable', 'shareholders')
            ->withPivot('percentage')
            ->withTimestamps()
            ->union($this->morphedByMany(Entity::class, 'shareholdable', 'shareholders')->withPivot('percentage')->withTimestamps());
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
    // Relationships to Users (Assigned AM, TM, SA)
    public function assignedAm()
    {
        return $this->belongsTo(User::class, 'assigned_am_id');
    }

    public function assignedTm()
    {
        return $this->belongsTo(User::class, 'assigned_tm_id');
    }

    public function assignedSa()
    {
        return $this->belongsTo(User::class, 'assigned_sa_id');
    }

    
}
