<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Tartibi MUHIM:
     *   1. Filiallar (boshqa jadvallar uchun FK)
     *   2. To'lov turlari
     *   3. Foydalanuvchilar (filial_id FK)
     *
     * Import uchun:
     *   php artisan nasiya:import hammasi --xodim-id=7
     *   (7 = import@nasiyapro.uz ning ID si)
     */
    public function run(): void
    {
        $this->call([
            FiliallarSeeder::class,
            TulovTurlariSeeder::class,
            FoydalanuvchilarSeeder::class,
        ]);
    }
}
