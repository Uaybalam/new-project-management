<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'address', 'notes', 'ssn',
        'ssn_itin_copy',
        'drivers_license',
        'drivers_license_copy',
        'date_of_birth',
        'pit_filing_status',
        'pit_copy',
        'spouse_first_name',
        'spouse_last_name',
        'spouse_ssn_itin',
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
     // Method to get the URL of the SSN/ITIN file
     public function getSsnItinCopyUrlAttribute()
     {
         return $this->ssn_itin_copy ? Storage::url($this->ssn_itin_copy) : null;
     }
 
     // Method to get the URL of the driver's license copy
     public function getDriversLicenseCopyUrlAttribute()
     {
         return $this->drivers_license_copy ? Storage::url($this->drivers_license_copy) : null;
     }
 
     // Method to get the URL of the PIT filing status copy
     public function getPitCopyUrlAttribute()
     {
         return $this->pit_copy ? Storage::url($this->pit_copy) : null;
     }
}
