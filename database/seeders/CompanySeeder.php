<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Company::create(
            [
                'name' => 'ООО "Строй Техно Инженеринг"',
                'short_name' => 'СТИ',
                'inn' => '7720734368',
            ]
        );
        Company::create(
            [
                'name' => 'ООО "БАМС-Трейд"',
                'short_name' => 'БАМС',
                'inn' => '7719754352'
            ]
        );
        Company::create(
            [
                'name' => 'ООО "ПРОМТЕХИНЖИНИРИНГ"',
                'short_name' => 'ПТИ',
                'inn' => '9701032984'
            ]
        );
        Company::create(
            [
                'name' => 'ООО "ДТ ТЕРМО ГРУПП"',
                'short_name' => 'ДТ',
                'inn' => '7702711544'
            ]
        );
    }
}
