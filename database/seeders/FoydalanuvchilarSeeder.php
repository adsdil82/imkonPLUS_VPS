<?php

namespace Database\Seeders;

use App\Models\Foydalanuvchi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FoydalanuvchilarSeeder extends Seeder
{
    /**
     * Boshlang'ich foydalanuvchilar.
     * MUHIM: Parollarni birinchi kirishdan keyin o'zgartiring!
     */
    public function run(): void
    {
        $foydalanuvchilar = [
            // Admin — barcha filiallar
            [
                'filial_id'    => null,
                'ism_familiya' => 'Tizim Admin',
                'email'        => 'admin@nasiyapro.uz',
                'password'     => Hash::make('Admin@2024!'),
                'rol'          => 'admin',
                'holat'        => 'faol',
            ],
            // Har bir filialda bitta menejer
            [
                'filial_id'    => 1,
                'ism_familiya' => 'Istiqlol Menejer',
                'email'        => 'ist.menejer@nasiyapro.uz',
                'password'     => Hash::make('Menejer@2024!'),
                'rol'          => 'menejer',
                'holat'        => 'faol',
            ],
            [
                'filial_id'    => 2,
                'ism_familiya' => 'Mingtut Menejer',
                'email'        => 'min.menejer@nasiyapro.uz',
                'password'     => Hash::make('Menejer@2024!'),
                'rol'          => 'menejer',
                'holat'        => 'faol',
            ],
            [
                'filial_id'    => 3,
                'ism_familiya' => 'Navoiy Menejer',
                'email'        => 'nav.menejer@nasiyapro.uz',
                'password'     => Hash::make('Menejer@2024!'),
                'rol'          => 'menejer',
                'holat'        => 'faol',
            ],
            [
                'filial_id'    => 4,
                'ism_familiya' => 'Turkiston Menejer',
                'email'        => 'tur.menejer@nasiyapro.uz',
                'password'     => Hash::make('Menejer@2024!'),
                'rol'          => 'menejer',
                'holat'        => 'faol',
            ],
            [
                'filial_id'    => 5,
                'ism_familiya' => 'Yakkatut Menejer',
                'email'        => 'yak.menejer@nasiyapro.uz',
                'password'     => Hash::make('Menejer@2024!'),
                'rol'          => 'menejer',
                'holat'        => 'faol',
            ],
            // Import uchun maxsus foydalanuvchi
            [
                'filial_id'    => null,
                'ism_familiya' => 'Import Xodimi',
                'email'        => 'import@nasiyapro.uz',
                'password'     => Hash::make('Import@2024!'),
                'rol'          => 'admin',
                'holat'        => 'nofaol', // Import tugagach nofaol qilish
            ],
        ];

        foreach ($foydalanuvchilar as $f) {
            Foydalanuvchi::updateOrCreate(
                ['email' => $f['email']],
                $f
            );
        }

        $this->command->info('Foydalanuvchilar (' . count($foydalanuvchilar) . ' ta) seed qilindi.');
        $this->command->warn('MUHIM: Admin parolini darhol o\'zgartiring!');
    }
}
