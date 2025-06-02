<?php

namespace Database\Seeders;

use App\Models\CryptoCurrency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CryptoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CryptoCurrency::insert([
            ['name' => 'btc', 'wallet_address' => '1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa'],
            ['name' => 'eth', 'wallet_address' => '0x742d35Cc6634C0532925a3b844Bc454e4438f44e'],
            ['name' => 'usdt', 'wallet_address' => 'TKFLguL3uHxe3sZvxPgHCjK5YftvuXsXE5'],
            ['name' => 'bnb', 'wallet_address' => 'bnb1jxfh2g85q3v0tdq56fnevx6xcxtcnhtsmcu64m'],
        ]);
    }
}
