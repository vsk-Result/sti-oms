<?php

namespace Database\Seeders;

use App\Models\Object\BObject;
use Illuminate\Database\Seeder;

class ObjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BObject::upsert(
            [
                'code' => '288',
                'name' => 'Музей ГЭС-2',
                'address' => 'Москва, Россия, 119072, Болотная набережная, 15к1',
            ],
            [
                'code' => '342',
                'name' => 'Отель Ритц-Карлтон',
                'address' => '125009 Москва ул. Тверская д. 3',
            ],
            [
                'code' => '346',
                'name' => 'Октафарма - Скопин',
                'address' => 'Скопин, Рязанская область, улица Карла Маркса, 88/17',
            ],
            [
                'code' => '349',
                'name' => 'ГК Патриот',
                'address' => '',
            ],
            [
                'code' => '353',
                'name' => 'ЖК "LION GATE"',
                'address' => 'г. Москва, ЦАО, Малый Сухаревский пер, вл. 4',
            ],
        );
    }
}
