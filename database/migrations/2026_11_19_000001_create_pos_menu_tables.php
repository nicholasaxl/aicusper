<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the POS menu tables (same names/columns as pos_revamp_h2) from database/sql/pos_menu_schema.sql.
 */
return new class extends Migration
{
    private array $tables = [
        'item_variant_outlet_data', 'item_variants', 'item_images', 'item_category_map', 'item_outlet_map',
        'item_categories', 'items', 'outlets', 'merchants',
    ];

    public function up(): void
    {
        if (Schema::hasTable('items')) {
            return; // already present, e.g. a database restored from POS
        }

        DB::unprepared(file_get_contents(database_path('sql/pos_menu_schema.sql')));
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::dropIfExists($table);
        }
    }
};
