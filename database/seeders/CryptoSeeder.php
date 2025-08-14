<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CryptoCurrency;

class CryptoSeeder extends Seeder
{
    public function run()
    {
        $cryptos = [
            ['name' => 'Bitcoin', 'symbol' => 'BTC', 'network' => 'Bitcoin', 'buy_rate' => 60000, 'sell_rate' => 59000],
            ['name' => 'Ethereum', 'symbol' => 'ETH', 'network' => 'ERC-20', 'buy_rate' => 4000, 'sell_rate' => 3900],
            ['name' => 'Tether', 'symbol' => 'USDT', 'network' => 'ERC-20', 'buy_rate' => 1, 'sell_rate' => 0.99],
            ['name' => 'Binance Coin', 'symbol' => 'BNB', 'network' => 'BEP-20', 'buy_rate' => 500, 'sell_rate' => 490],
        ];

        foreach ($cryptos as $crypto) {
            CryptoCurrency::create($crypto);
        }
    }
}