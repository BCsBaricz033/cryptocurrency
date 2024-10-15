<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Codenixsv\CoinGeckoApi\CoinGeckoClient;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'balance' => 500
        ]);



        $client = new CoinGeckoClient();

        $marketData = $client->coins()->getMarkets('usd');

        foreach ($marketData as $coin) {
            DB::table('coins')->insert([
                'id' => $coin['id'],
                'name' => $coin['name'],
                'price' => $coin['current_price'],
                'price_change_percentage_24h' => $coin['price_change_percentage_24h'],
                'price_change_24h' => $coin['price_change_24h'],
                'image' => $coin['image'],
            ]);
        }
    }
}
