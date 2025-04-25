<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'unit_price',
        'service',
    ];
    public function engagements()
{
    return $this->belongsToMany(Engagement::class, 'engagement_product')
        ->withPivot('quantity', 'unit_price', 'subtotal')
        ->withTimestamps();
}
}
