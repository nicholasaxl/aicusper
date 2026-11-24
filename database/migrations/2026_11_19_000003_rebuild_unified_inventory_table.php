<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Replaces the hand-entered unified_inventory_table with a per-outlet copy of the POS menu.
 * It is filled by the "Sync menu" button (ProductController::syncMenu), so the product page,
 * the AI labelling and the recommender all read one flat table instead of joining POS tables.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('unified_inventory_table');

        Schema::create('unified_inventory_table', function (Blueprint $table) {
            $table->id('inventory_id');
            $table->unsignedBigInteger('merchant_id');
            $table->unsignedBigInteger('outlet_id');
            $table->unsignedBigInteger('item_id');          // POS items.id
            $table->string('name', 255);
            $table->string('category', 255)->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->text('image_path')->nullable();         // path inside storage/app/public
            $table->boolean('is_active')->default(true);    // sold and enabled at this outlet
            $table->jsonb('labels')->nullable();            // AI labels (copy of the label store)
            $table->timestampTz('synced_at')->nullable();

            $table->unique(['outlet_id', 'item_id']);
            $table->index('merchant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unified_inventory_table');
    }
};
