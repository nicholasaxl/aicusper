<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('unified_inventory_table', function (Blueprint $table) {
            $table->id('inventory_id'); // SERIAL PRIMARY KEY
            $table->integer('outlet_id')->notNullable();
            $table->string('outlet_name', 255)->nullable();
            $table->string('item_name', 255)->nullable();
            $table->text('image_path')->nullable();

            $table->enum('active', ['active', 'inactive'])
                  ->default('active');

            $table->decimal('price', 12, 2)->nullable();
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unified_inventory_table');
    }
};
