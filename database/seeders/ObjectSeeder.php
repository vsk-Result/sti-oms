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
                'photo' => null,
            ]
        );
        BObject::create(
            [
                'code' => '27',
                'name' => 'Офис / Склад',
                'address' => '',
                'photo' => 'https://it.dttermo.ru/storage/objects/preview/thumbs/21_Gg_uley-plaza-moskva-546940933-6.jpg',
            ]
        );
        BObject::create(
            [
                'code' => '257',
                'name' => 'НИИ Транснефть',
                'address' => 'г. Москва, Севастопольский проспект, д. 47А',
                'photo' => 'https://it.dttermo.ru/storage/objects/preview/thumbs/14_I6_nii_tnn.jpg',
            ]
        );
        BObject::create(
            [
                'code' => '268',
                'name' => 'Башня Эволюция',
                'address' => 'Москва, Россия, 123317, Пресненская набережная, 4с2',
                'photo' => 'https://it.dttermo.ru/storage/objects/preview/thumbs/11_OD_10.jpg',
            ]
        );
        BObject::create(
            [
                'code' => '288',
                'name' => 'Музей ГЭС-2',
                'address' => 'Москва, Россия, 119072, Болотная набережная, 15к1',
                'photo' => 'https://it.dttermo.ru/storage/objects/preview/thumbs/12_Oz_ges5.jpg',
            ]
        );
        BObject::create(
            [
                'code' => '304',
                'name' => 'Малая Ордынка',
                'address' => 'г. Москва, Малая Ордынка, д. 19',
                'photo' => 'https://it.dttermo.ru/storage/objects/preview/thumbs/16_mf_Malaya_Ordinka.jpg',
            ]
        );
        BObject::create(
            [
                'code' => '317',
                'name' => 'Патриарший мост',
                'address' => '',
                'photo' => null,
            ]
        );
        BObject::create(
            [
                'code' => '321',
                'name' => 'Садовые кварталы',
                'address' => 'г. Москва, ЦАО, район Хамовники, улица Усачева, вл. 11',
                'photo' => 'https://it.dttermo.ru/storage/objects/preview/thumbs/L9HUp0_Sadovye_Kvartaly.jpg',
            ]
        );
        BObject::create(
            [
                'code' => '325',
                'name' => 'Благоустройство',
                'address' => '',
                'photo' => null,
            ]
        );
        BObject::create(
            [
                'code' => '327',
                'name' => 'Дон Строй',
                'address' => 'Погодинская улица, вл.2/3, стр.1, 2, 3, 4, район Хамовники, ЦАО г. Москвы, секция 5',
                'photo' => 'https://it.dttermo.ru/storage/objects/preview/thumbs/nVbPvZ_2XdhVA_20190924_135819.jpg',
            ]
        );
        BObject::create(
            [
                'code' => '332',
                'name' => 'Гута',
                'address' => '',
                'photo' => null,
            ]
        );
        BObject::create(
            [
                'code' => '335',
                'name' => 'Скандинавская больница',
                'address' => '111024 г. Москва, 2-я Кабельная улица, д. 2, строение 25',
                'photo' => 'https://it.dttermo.ru/storage/objects/preview/thumbs/cWE7wD_XXL.jpg',
            ]
        );
        BObject::create(
            [
                'code' => '338',
                'name' => 'Бродский',
                'address' => 'г. Москва, 1-й Тружеников пер., вл.16-18',
                'photo' => 'https://it.dttermo.ru/storage/objects/preview/thumbs/6uXWH1_4984936_60e16cb3cf3440de0dec9a5e70b22c37.[w-850_h-550_strict-1_q-75].jpg',
            ]
        );
        BObject::create(
            [
                'code' => '342',
                'name' => 'Отель Ритц-Карлтон',
                'address' => '125009 Москва ул. Тверская д. 3',
                'photo' => 'https://it.dttermo.ru/storage/objects/preview/thumbs/lbTtUC_Ritts_Karlton.jpg',
            ]
        );
        BObject::create(
            [
                'code' => '344',
                'name' => 'Дом Николино',
                'address' => 'МО, Одинцовский район, Деревня Таганьково, гп 7, участок 135',
                'photo' => null,
            ]
        );
        BObject::create(
            [
                'code' => '346',
                'name' => 'Октафарма - Скопин',
                'address' => 'Скопин, Рязанская область, улица Карла Маркса, 88/17',
                'photo' => 'https://it.dttermo.ru/storage/objects/preview/thumbs/bLsM1p_Oktafarma.jpg',
            ]
        );
        BObject::create(
            [
                'code' => '349',
                'name' => 'ГК Патриот',
                'address' => '',
                'photo' => 'https://it.dttermo.ru/storage/objects/preview/thumbs/bB5axu_b3b2ccb293772b3e37c81d89193e3c7b.jpg',
            ]
        );
        BObject::create(
            [
                'code' => '350',
                'name' => 'Роза Росса',
                'address' => 'г. Москва, ул. Зубовская, вл. 7',
                'photo' => null,
            ]
        );
        BObject::create(
            [
                'code' => '352',
                'name' => 'Эвалар',
                'address' => '',
                'photo' => 'https://it.dttermo.ru/storage/objects/preview/thumbs/19_lh_evalar.JPG',
            ]
        );
        BObject::create(
            [
                'code' => '353',
                'name' => 'ЖК "LION GATE"',
                'address' => 'г. Москва, ЦАО, Малый Сухаревский пер, вл. 4',
                'photo' => 'https://it.dttermo.ru/storage/objects/preview/thumbs/M343JA_LionGate.jpg',
            ]
        );
        BObject::create(
            [
                'code' => '354',
                'name' => 'Новороссийск',
                'address' => '',
                'photo' => null,
            ]
        );
        BObject::create(
            [
                'code' => '355',
                'name' => 'Квартира VTB арена парк',
                'address' => '',
                'photo' => null,
            ]
        );
        BObject::create(
            [
                'code' => '356',
                'name' => 'Квартира Ларисы Викторовны',
                'address' => 'м. Авиамоторная',
                'photo' => null,
            ]
        );
    }
}
