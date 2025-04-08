<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
        'price',
        'model',
        'description',
        'photo',
        'stock',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'float',
        'stock' => 'integer',
    ];

    /**
     * Check if product is in stock
     */
    public function isInStock()
    {
        return $this->stock > 0;
    }

    /**
     * Reduce stock by given quantity
     */
    public function reduceStock($quantity = 1)
    {
        if ($this->stock < $quantity) {
            return false;
        }
        
        $this->stock -= $quantity;
        $this->save();
        return true;
    }

    /**
     * Get all purchases of this product
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}

