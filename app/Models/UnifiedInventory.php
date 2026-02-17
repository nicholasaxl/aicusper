<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnifiedInventory extends Model
{
    protected $table = 'unified_inventory_table';

    protected $primaryKey = 'inventory_id';

    public $timestamps = false;

    protected $fillable = [
        'outlet_id',
        'outlet_name',
        'item_name',
        'image_path',
        'active',
        'price',
        'description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];
}
