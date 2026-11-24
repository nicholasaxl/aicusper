<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Local/demo login only. Run with: php artisan db:seed --class=DemoUserSeeder
 * Do not run against a production database.
 */
class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PosMenuSeeder::class); // test merchant/outlet + menu the demo user manages

        User::updateOrCreate(
            ['email' => 'demo@aicusper.test'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('Admin123'),
                'email_verified_at' => now(),
                'outlet_id' => PosMenuSeeder::OUTLET_ID,
            ]
        );
    }
}
