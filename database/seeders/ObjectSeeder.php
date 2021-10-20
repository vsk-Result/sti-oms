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
        BObject::create(
            [
                'code' => '104',
                'name' => 'Завидово',
                'address' => '',
            ]
        );
        BObject::create(
            [
                'code' => '27',
                'name' => 'Офис / Склад',
                'address' => '',
            ]
        );
        BObject::create(
            [
                'code' => '257',
                'name' => 'НИИ Транснефть',
                'address' => 'г. Москва, Севастопольский проспект, д. 47А',
            ]
        );
        BObject::create(
            [
                'code' => '268',
                'name' => 'Башня Эволюция',
                'address' => 'Москва, Россия, 123317, Пресненская набережная, 4с2',
            ]
        );
        BObject::create(
            [
                'code' => '288',
                'name' => 'Музей ГЭС-2',
                'address' => 'Москва, Россия, 119072, Болотная набережная, 15к1',
            ]
        );
        BObject::create(
            [
                'code' => '304',
                'name' => 'Малая Ордынка',
                'address' => 'г. Москва, Малая Ордынка, д. 19',
            ]
        );
        BObject::create(
            [
                'code' => '317',
                'name' => 'Патриарший мост',
                'address' => '',
            ]
        );
        BObject::create(
            [
                'code' => '321',
                'name' => 'Садовые кварталы',
                'address' => 'г. Москва, ЦАО, район Хамовники, улица Усачева, вл. 11',
            ]
        );
        BObject::create(
            [
                'code' => '325',
                'name' => 'Благоустройство',
                'address' => '',
            ]
        );
        BObject::create(
            [
                'code' => '327',
                'name' => 'Дон Строй',
                'address' => 'Погодинская улица, вл.2/3, стр.1, 2, 3, 4, район Хамовники, ЦАО г. Москвы, секция 5',
            ]
        );
        BObject::create(
            [
                'code' => '332',
                'name' => 'Гута',
                'address' => '',
            ]
        );
        BObject::create(
            [
                'code' => '335',
                'name' => 'Скандинавская больница',
                'address' => '111024 г. Москва, 2-я Кабельная улица, д. 2, строение 25',
            ]
        );
        BObject::create(
            [
                'code' => '338',
                'name' => 'Бродский',
                'address' => 'г. Москва, 1-й Тружеников пер., вл.16-18',
            ]
        );
        BObject::create(
            [
                'code' => '342',
                'name' => 'Отель Ритц-Карлтон',
                'address' => '125009 Москва ул. Тверская д. 3',
            ]
        );
        BObject::create(
            [
                'code' => '344',
                'name' => 'Дом Николино',
                'address' => 'МО, Одинцовский район, Деревня Таганьково, гп 7, участок 135',
            ]
        );
        BObject::create(
            [
                'code' => '346',
                'name' => 'Октафарма - Скопин',
                'address' => 'Скопин, Рязанская область, улица Карла Маркса, 88/17',
            ]
        );
        BObject::create(
            [
                'code' => '349',
                'name' => 'ГК Патриот',
                'address' => '',
            ]
        );
        BObject::create(
            [
                'code' => '350',
                'name' => 'Роза Росса',
                'address' => 'г. Москва, ул. Зубовская, вл. 7',
            ]
        );
        BObject::create(
            [
                'code' => '352',
                'name' => 'Эвалар',
                'address' => '',
            ]
        );
        BObject::create(
            [
                'code' => '353',
                'name' => 'ЖК "LION GATE"',
                'address' => 'г. Москва, ЦАО, Малый Сухаревский пер, вл. 4',
            ]
        );
        BObject::create(
            [
                'code' => '354',
                'name' => 'Новороссийск',
                'address' => '',
            ]
        );
        BObject::create(
            [
                'code' => '355',
                'name' => 'Квартира VTB арена парк',
                'address' => '',
            ]
        );
        BObject::create(
            [
                'code' => '356',
                'name' => 'Квартира Ларисы Викторовны',
                'address' => 'м. Авиамоторная',
            ]
        );
    }
}
