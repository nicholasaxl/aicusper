<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Fake POS data for local testing: one test merchant + outlet with a sample menu spread over the POS
 * tables (items, item_outlet_map, categories, images, variants). Descriptions are left empty on purpose
 * (most real POS items have none, which is what the AI labelling is for).
 *
 * This is the stand-in for the real POS database; unified_inventory_table is filled from it by the
 * "Sync menu" button on the product page.
 *
 * Re-runnable. Local/demo only.
 */
class PosMenuSeeder extends Seeder
{
    public const MERCHANT_ID = 9001;
    public const OUTLET_ID = 9001;

    private const ITEM_ID_OFFSET = 90000; // item id = offset + sample number

    /** [sample no, name, price, active, drink?, image path in storage/app/public] */
    private const SAMPLE_MENU = [
        [1, 'Chicken Nanban', 120000, true, false, 'products/af7d3c48-acda-4816-9898-a783942f1a0b.jpg'],
        [3, 'Cold Ocha', 120000, true, true, 'products/189b2c68-fd27-4337-addc-9c3cb24c9ca9.jpg'],
        [4, 'Ayam Popcorn', 20000, true, false, 'products/166b104a-acb9-476e-a1ec-91dbf67a14a1.jpg'],
        [5, 'Risotto', 120000, true, false, 'products/b6397405-df68-4171-a72b-8b052d1cf6d4.jpg'],
        [6, 'Pizza', 2133, true, false, 'products/59a62496-0324-4cb9-8680-7d848118dd43.jpg'],
        [10, 'Soto Rawon', 100000, true, false, 'products/2568ecdd-b33d-49f7-b5e4-24a1d36e989f.jpeg'],
        [11, 'Butter Chicken with Naan', 123123, true, false, 'products/22b16e7c-fb6b-400f-8a65-444d38c57809.jpg'],
        [12, 'Pork Signature Ramen', 345674, true, false, 'products/9c578a08-dcb8-4751-ba62-263f82227186.jpg'],
        [13, 'Cold Sprite', 123123, false, true, 'products/2a163fed-04e5-445c-b0a8-822598e9c984.jpg'],
        [14, 'Hot Chocolate', 90000, true, true, 'products/12550699-0d4f-4d3b-90e0-af6d5199dbad.jpg'],
        [15, 'Cold Coca Cola', 45000, true, true, 'products/58f97057-0dc3-49e2-af81-81317b829beb.jpg'],
        [17, 'Breakfast Burrito', 12000, false, false, 'products/81e1f6a9-e582-4ded-afe2-c4804cc770a5.jpg'],
        [18, 'Americano', 123, true, true, 'products/91457da3-fa78-4c5e-9b5a-a95373fc26dd.jpg'],
    ];

    public function run(): void
    {
        $now = now();
        $merchant = self::MERCHANT_ID;
        $outlet = self::OUTLET_ID;
        $categories = ['Makanan' => 9001, 'Minuman' => 9002];

        DB::transaction(function () use ($now, $merchant, $outlet, $categories) {
            DB::table('merchants')->updateOrInsert(['id' => $merchant], [
                'name' => 'Test Merchant', 'created_by' => 1, 'created_at' => $now,
                'address' => 'Jl. Senopati No.83, Jakarta Selatan', 'status_id' => 'active',
                'merchant_code' => 'TEST9001',
            ]);

            DB::table('outlets')->updateOrInsert(['id' => $outlet], [
                'merchant_id' => $merchant, 'name' => 'Test Outlet - JH Kitchen', 'is_open' => true,
                'created_by' => 1, 'created_at' => $now, 'status_id' => 'active',
                'address' => 'Jl. Senopati No.83, Jakarta Selatan', 'timezone' => 'Asia/Jakarta',
            ]);

            foreach ($categories as $name => $id) {
                DB::table('item_categories')->updateOrInsert(['id' => $id], [
                    'merchant_id' => $merchant, 'name' => $name, 'is_active' => true,
                    'is_popular' => false, 'is_deleted' => false, 'created_at' => $now,
                ]);
            }

            foreach (self::SAMPLE_MENU as [$no, $name, $price, $active, $isDrink, $imagePath]) {
                $itemId = self::ITEM_ID_OFFSET + $no;
                $variantId = $itemId; // one default variant per item

                DB::table('items')->updateOrInsert(['id' => $itemId], [
                    'merchant_id' => $merchant, 'name' => $name, 'desc' => null,
                    'created_by' => 1, 'created_at' => $now, 'sellable' => true, 'is_bundle' => false,
                    'base_unit_price' => $price, 'is_active' => $active,
                ]);

                DB::table('item_category_map')->updateOrInsert(
                    ['item_id' => $itemId, 'category_id' => $categories[$isDrink ? 'Minuman' : 'Makanan']], []
                );

                DB::table('item_outlet_map')->updateOrInsert(
                    ['item_id' => $itemId, 'outlet_id' => $outlet], ['is_active' => $active]
                );

                DB::table('item_images')->updateOrInsert(['id' => $itemId], [
                    'item_id' => $itemId, 'image_path' => $imagePath, 'is_main' => true, 'is_deleted' => false,
                ]);

                DB::table('item_variants')->updateOrInsert(['id' => $variantId], [
                    'item_id' => $itemId, 'merchant_id' => $merchant,
                    'base_variant_unit_price' => $price, 'is_bundle' => false, 'is_deleted' => false,
                ]);

                DB::table('item_variant_outlet_data')->updateOrInsert(['id' => $variantId], [
                    'outlet_id' => $outlet, 'item_variant_id' => $variantId, 'is_active' => $active,
                ]);
            }

            // keep the id sequences ahead of the fixed ids used above
            foreach (['merchants', 'outlets', 'items', 'item_categories', 'item_images', 'item_variants'] as $table) {
                DB::statement("SELECT setval(pg_get_serial_sequence('{$table}', 'id'), (SELECT MAX(id) FROM {$table}))");
            }
            DB::statement("SELECT setval(pg_get_serial_sequence('item_variant_outlet_data', 'id'), (SELECT MAX(id) FROM item_variant_outlet_data))");
        });
    }
}
