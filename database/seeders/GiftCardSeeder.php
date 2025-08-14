<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GiftCard;

class GiftCardSeeder extends Seeder
{
    public function run()
    {
        $giftCards = [
            [
                'name' => 'Amazon',
                'category' => 'Retail',
                'denomination' => 50.00,
                'buy_rate' => 0.80,
                'sell_rate' => 0.95,
                'is_enabled' => true,
                'stock' => 100,
                'image' => 'giftcards/amazon.png', // Sample path
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'iTunes',
                'category' => 'Entertainment',
                'denomination' => 25.00,
                'buy_rate' => 0.75,
                'sell_rate' => 0.90,
                'is_enabled' => true,
                'stock' => 50,
                'image' => 'giftcards/itunes.png', // Sample path
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Steam',
                'category' => 'Gaming',
                'denomination' => 20.00,
                'buy_rate' => 0.70,
                'sell_rate' => 0.85,
                'is_enabled' => false,
                'stock' => 20,
                'image' => 'giftcards/steam.png', // Sample path
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($giftCards as $giftCardData) {
            GiftCard::create($giftCardData);
        }
    }
}