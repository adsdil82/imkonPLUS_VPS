<?php

namespace Database\Seeders;

use App\Models\Filial;
use Illuminate\Database\Seeder;

class FiliallarSeeder extends Seeder
{
    /**
     * Filiallar — ID 1-5 bo'lishi MUHIM!
     * SQL import fayllarida filial_kod 1-5 raqam bilan berilgan.
     * ID lar sequence bilan avtomatik bo'ladi, lekin 1 dan boshlanadi.
     */
    public function run(): void
    {
        $filiallar = [
            [
                'id'     => 1,
                'nomi'   => 'Istiqlol filiali',
                'kod'    => 'IST',
                'manzil' => 'Toshkent sh., Istiqlol ko\'chasi',
                'holat'  => 'faol',
            ],
            [
                'id'     => 2,
                'nomi'   => 'Mingtut filiali',
                'kod'    => 'MIN',
                'manzil' => 'Toshkent sh., Mingtut ko\'chasi',
                'holat'  => 'faol',
            ],
            [
                'id'     => 3,
                'nomi'   => 'Navoiy filiali',
                'kod'    => 'NAV',
                'manzil' => 'Toshkent sh., Navoiy ko\'chasi',
                'holat'  => 'faol',
            ],
            [
                'id'     => 4,
                'nomi'   => 'Turkiston filiali',
                'kod'    => 'TUR',
                'manzil' => 'Toshkent sh., Turkiston ko\'chasi',
                'holat'  => 'faol',
            ],
            [
                'id'     => 5,
                'nomi'   => 'Yakkatut filiali',
                'kod'    => 'YAK',
                'manzil' => 'Toshkent sh., Yakkatut ko\'chasi',
                'holat'  => 'faol',
            ],
        ];

        foreach ($filiallar as $filial) {
            Filial::updateOrCreate(['id' => $filial['id']], $filial);
        }

        $this->command->info('Filiallar (5 ta) seed qilindi.');
    }
}
