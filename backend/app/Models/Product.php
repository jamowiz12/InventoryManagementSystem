<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = ['category_id', 'supplier_id', 'product_name', 'description', 'unit_price', 'stock_quantity', 'minimum_stock'];

    protected function casts(): array
    {
        return ['unit_price' => 'decimal:2', 'stock_quantity' => 'integer', 'minimum_stock' => 'integer'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(StockTransaction::class);
    }
}
