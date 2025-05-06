<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Models\Entity; 
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Shareholder extends Model
{
    protected $table = 'shareholders';

    protected $fillable = [
        'entity_id',
        'shareholdable_type',
        'shareholdable_id',
        'percentage',
    ];
    public function shareholdable(): MorphTo
    {
        return $this->morphTo();
    }

    public function entity()
    {
        return $this->belongsTo(\App\Models\Entity::class);
    }
}