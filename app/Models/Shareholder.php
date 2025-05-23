<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Models\Entity; 

class Shareholder extends Pivot
{
    protected $table = 'shareholders';

    protected $fillable = [
        'entities_id',
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