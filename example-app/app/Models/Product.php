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
        'stock_amount',
    ];

    public function isEven(): bool
    {
        return $this->id % 2 === 0;
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
