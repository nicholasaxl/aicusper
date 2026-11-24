<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One row per menu item per outlet: a flat copy of the POS menu (items, item_outlet_map,
 * categories, images) plus the AI labels. Filled by ProductController::syncMenu().
 */
class UnifiedInventory extends Model
{
    protected $table = 'unified_inventory_table';

    protected $primaryKey = 'inventory_id';

    public $timestamps = false;

    protected $fillable = [
        'merchant_id',
        'outlet_id',
        'item_id',
        'name',
        'category',
        'description',
        'price',
        'image_path',
        'is_active',
        'labels',
        'synced_at',
    ];

    protected $casts = [
        'price'     => 'decimal:2',
        'is_active' => 'boolean',
        'labels'    => 'array',
        'synced_at' => 'datetime',
    ];
}
