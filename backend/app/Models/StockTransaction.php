<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransaction extends Model
{
    public $timestamps = false;

    protected $fillable = ['product_id', 'transaction_type', 'quantity', 'previous_stock', 'new_stock', 'remarks', 'created_at'];

    protected function casts(): array
    {
        return ['quantity' => 'integer', 'previous_stock' => 'integer', 'new_stock' => 'integer', 'created_at' => 'datetime'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
