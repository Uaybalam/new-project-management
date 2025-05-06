<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Shareholder;


class Entity extends Model
{
    use HasFactory;

    protected $fillable = [ 'name', 'phone', 'email', 'notes',
    'entity_id', 'entity_status', 'website', 'billing_address', 'business_address',
    'document_folder_link', 'incorporation_date', 'formally_known_as', 'doing_business_as',
    'effective_entity_type_date', 'state_of_registration', 'industry', 'number_of_employees',
    'revenue_range', 'assigned_am_id', 'assigned_tm_id', 'assigned_sa_id'];



    public function shareholders()
{
    return $this->hasMany(\App\Models\Shareholder::class);
}
        // Optional helpers
    public function contactShareholders()
    {
        return $this->shareholders()->where('shareholdable_type', Contact::class);
    }

    public function entityShareholders()
    {
        return $this->shareholders()->where('shareholdable_type', Entity::class);
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
