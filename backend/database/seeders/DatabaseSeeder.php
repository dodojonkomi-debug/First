<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Region;
use App\Models\District;
use App\Models\School;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Суперадмин
        User::create([
            'name' => 'Суперадмин',
            'email' => 'admin@mtmu.tj',
            'password' => Hash::make('admin123'),
            'role' => 'superadmin',
        ]);

        // Вилоятҳо
        $regions = [
            ['name' => 'Вилояти Суғд', 'code' => 'SUGHD'],
            ['name' => 'Вилояти Хатлон', 'code' => 'KHATLON'],
            ['name' => 'ВМКБ', 'code' => 'GBAO'],
            ['name' => 'Ноҳияҳои тобеи ҷумҳурӣ', 'code' => 'DRS'],
            ['name' => 'Шаҳри Душанбе', 'code' => 'DUSHANBE'],
        ];

        foreach ($regions as $regionData) {
            $region = Region::create($regionData);

            // Ноҳияҳои намунавӣ
            $districts = match ($region->code) {
                'SUGHD' => [
                    ['name' => 'Шаҳри Хуҷанд', 'code' => 'SUGHD-KHJ'],
                    ['name' => 'Ноҳияи Бобоҷон Ғафуров', 'code' => 'SUGHD-BG'],
                    ['name' => 'Ноҳияи Истаравшан', 'code' => 'SUGHD-IST'],
                ],
                'KHATLON' => [
                    ['name' => 'Шаҳри Бохтар', 'code' => 'KHATLON-BOK'],
                    ['name' => 'Ноҳияи Ёвон', 'code' => 'KHATLON-YAV'],
                    ['name' => 'Ноҳияи Ваҳдат', 'code' => 'KHATLON-VAH'],
                ],
                'DUSHANBE' => [
                    ['name' => 'Ноҳияи Фирдавсӣ', 'code' => 'DUSH-FIR'],
                    ['name' => 'Ноҳияи Сино', 'code' => 'DUSH-SIN'],
                    ['name' => 'Ноҳияи Исмоили Сомонӣ', 'code' => 'DUSH-ISM'],
                    ['name' => 'Ноҳияи Шоҳмансур', 'code' => 'DUSH-SHM'],
                ],
                default => [
                    ['name' => 'Ноҳияи 1', 'code' => $region->code . '-N1'],
                    ['name' => 'Ноҳияи 2', 'code' => $region->code . '-N2'],
                ],
            };

            foreach ($districts as $districtData) {
                $district = District::create([
                    ...$districtData,
                    'region_id' => $region->id,
                ]);

                // 2 мактаб барои ҳар ноҳия
                for ($i = 1; $i <= 2; $i++) {
                    School::create([
                        'district_id' => $district->id,
                        'name' => "Мактаби тахассусии №{$i} - {$district->name}",
                        'code' => $district->code . "-SCH{$i}",
                        'capacity_class_0' => 30,
                        'capacity_class_1' => 35,
                    ]);
                }
            }

            // Маъмури вилоят
            User::create([
                'name' => "Маъмури {$region->name}",
                'email' => strtolower($region->code) . '@mtmu.tj',
                'password' => Hash::make('admin123'),
                'role' => 'admin_region',
                'region_id' => $region->id,
            ]);
        }

        // Волидайни намунавӣ
        User::create([
            'name' => 'Алиев Ахмад',
            'email' => 'parent@test.tj',
            'phone' => '+992901234567',
            'password' => Hash::make('parent123'),
            'role' => 'parent',
        ]);
    }
}
