<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Entity; 

class Engagement extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_id',
        'status',
        'start_date',
        'end_date',
        'notes',
        'sign_url',
    ];

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'engagement_product')
            ->withPivot('quantity', 'unit_price', 'subtotal')
            ->withTimestamps();
    }
}
