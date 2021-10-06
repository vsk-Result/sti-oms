<?php

namespace App\Services;

use App\Helpers\Sanitizer;
use App\Models\Payment;
use App\Models\Status;

class PaymentService
{
    private Sanitizer $sanitizer;

    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function createPayment(array $requestData): Payment
    {
        if (array_key_exists('base_payment_id', $requestData)) {
            $basePayment = Payment::find($requestData['base_payment_id']);
            $requestData = $basePayment->attributesToArray();
        }

        $payment = Payment::create([
            'statement_id' => $requestData['statement_id'],
            'company_id' => $requestData['company_id'],
            'bank_id' => $requestData['bank_id'],
            'object_id' => $requestData['object_id'],
            'object_worktype_id' => $requestData['object_worktype_id'],
            'organization_sender_id' => $requestData['organization_sender_id'],
            'organization_receiver_id' => $requestData['organization_receiver_id'],
            'type_id' => $requestData['type_id'],
            'payment_type_id' => $requestData['payment_type_id'],
            'category' => $requestData['category'],
            'code' => $this->sanitizer->set($requestData['code'])->toCode()->get(),
            'description' => $this->sanitizer->set($requestData['description'])->upperCaseFirstWord()->get(),
            'date' => $requestData['date'],
            'amount' => $requestData['amount'],
            'amount_without_nds' => $requestData['amount_without_nds'],
            'status_id' => $requestData['status_id']
        ]);

        return $payment;
    }

    public function updatePayment(Payment $payment, array $requestData): Payment
    {
        if (array_key_exists('amount', $requestData)) {
            $description = array_key_exists('description', $requestData) ? $requestData['description'] : $payment->description;
            $requestData['amount'] = $this->sanitizer->set($requestData['amount'])->toAmount()->get();
            $nds = $this->checkHasNDSFromDescription($description) ? round($requestData['amount'] / 6, 2) : 0;
            $requestData['amount_without_nds'] = $requestData['amount'] - $nds;
        } elseif (array_key_exists('object_id', $requestData)) {
            if (str_contains($requestData['object_id'], '::')) {
                $objectData = explode('::', $requestData['object_id']);
                $requestData['type_id'] = Payment::TYPE_OBJECT;
                $requestData['object_id'] = (int) $objectData[0];
                $requestData['object_worktype_id'] = (int) $objectData[1];
            } else {
                $requestData['type_id'] = (int) $requestData['object_id'];
                $requestData['object_id'] = null;
                $requestData['object_worktype_id'] = null;
            }
        } elseif (array_key_exists('code', $requestData)) {
            $requestData['code'] = $this->sanitizer->set($requestData['code'])->toCode()->get();
        }

        $payment->update($requestData);

        if (
            $payment->type !== Payment::TYPE_NONE
            && ! is_null($payment->code)
            && ! is_null($payment->description)
            && ! is_null($payment->category)
            && ! is_null($payment->amount)
        ) {
            if (! $payment->isActive()) {
                $payment->setActive();
            }
        } else {
            if (! $payment->isBlocked()) {
                $payment->setBlocked();
            }
        }

        return $payment;
    }

    public function destroyPayment(Payment $payment): Payment
    {
        $payment->delete();

        return $payment;
    }

    public function checkHasNDSFromDescription(string $description): bool
    {
        $description = $this->sanitizer->set($description)->noSpaces()->lowerCase()->get();

        if (
            ! str_contains($description, 'вт.ч.ндс')
            && ! str_contains($description, 'втомчислендс')
        ) {
            return false;
        }

        return true;
    }

    public function findCategoryFromDescription(string $description): ?string
    {
        $description = $this->sanitizer->set($description)->lowerCase()->get();
        $category = null;
        if (
            str_contains($description, 'комиссия')
            || str_contains($description, 'комиссии')
            || str_contains($description, 'межбанковские платежи')
            || str_contains($description, 'налог на доходы')
            || str_contains($description, 'штраф за нарушение')
            || str_contains($description, 'внутрибанковских платежей')
            || str_contains($description, 'исполнительного долга')
            || str_contains($description, 'алиментов')
            || str_contains($description, 'алименты')
            || str_contains($description, 'погашение начисленных процентов')
            || str_contains($description, 'удержание неустойки по алиментам')
            || str_contains($description, 'государственная пошлина')
            || str_contains($description, 'административный штраф')
            || str_contains($description, 'пополнение')
            || str_contains($description, 'использование кредита')
            || str_contains($description, 'перевод собственных средств')
            || str_contains($description, 'перечисление отпускных')
            || str_contains($description, 'перечисление денежных средств')
            || str_contains($description, 'перевод займа')
            || str_contains($description, 'оплата очередного взноса')
            || str_contains($description, 'выплата по больничному листу')
            || str_contains($description, 'оплата задолженности')
            || str_contains($description, 'использование кредита')
            || str_contains($description, 'пополнение корпоративного карточного счета')
            || str_contains($description, 'страховые взносы')
            || str_contains($description, 'возмещение расходов подрядчика')
            || str_contains($description, 'оплата страхового депозита')
            || str_contains($description, 'мирового соглашения')
            || str_contains($description, 'налог на добавленную стоимость')
            || str_contains($description, 'возврат депозита')
        ) {
            $category = Payment::CATEGORY_OPSTE;
        } elseif (
            str_contains($description, 'выполнение работ')
            || str_contains($description, 'работы')
            || str_contains($description, 'выполнение комплекса работ')
            || str_contains($description, 'аренда')
            || str_contains($description, 'аренду')
            || str_contains($description, 'аренды')
            || str_contains($description, 'услуга')
            || str_contains($description, 'услуги')
            || str_contains($description, 'услуге')
            || str_contains($description, 'пакета услуг')
            || str_contains($description, 'пакет услуг')
            || str_contains($description, 'право на использование электронной базы данных')
            || str_contains($description, 'интернет')
            || str_contains($description, 'вывоз мусора')
            || str_contains($description, 'проживание')
            || str_contains($description, 'услуг по питанию')
            || str_contains($description, 'выполненные смр')
            || str_contains($description, 'разработку')
            || str_contains($description, 'разработка')
            || str_contains($description, 'изготовление')
            || str_contains($description, 'доставк')
            || str_contains($description, 'ремонт')
            || str_contains($description, 'заправка картриджа')
            || str_contains($description, 'вывоз строительных отходов')
            || str_contains($description, 'комплекс работ')
            || str_contains($description, 'комплексные обеды')
            || str_contains($description, 'смр по')
            || str_contains($description, 'обслуживание')
            || str_contains($description, 'питание')
            || str_contains($description, 'очистка')
            || str_contains($description, 'очистке')
            || str_contains($description, 'организацию')
            || str_contains($description, 'организация')
            || str_contains($description, 'электронной базы данных')
            || str_contains($description, 'доступ к')
            || str_contains($description, 'охрану объекта')
            || str_contains($description, 'алмазное сверление')
            || str_contains($description, 'проведение')
            || str_contains($description, 'определение плотности')
            || str_contains($description, 'водоснабжение')
            || str_contains($description, 'таможенные операции')
            || str_contains($description, 'диагностик')
            || str_contains($description, 'абонентской платы')
            || str_contains($description, 'объявлени')
            || str_contains($description, 'предоставление')
            || str_contains($description, 'оказание')
            || str_contains($description, 'авансовые платежи для таможни')
        ) {
            $category = Payment::CATEGORY_RAD;
        } elseif (
            str_contains($description, 'канцтовары')
            || str_contains($description, 'тройник')
            || str_contains($description, 'розетк')
            || str_contains($description, 'станок')
            || str_contains($description, 'фьюзер')
            || str_contains($description, 'принтер')
            || str_contains($description, 'кислород')
            || str_contains($description, 'азот')
            || str_contains($description, 'материал')
            || str_contains($description, 'труб')
            || str_contains($description, 'полотн')
            || str_contains($description, 'плит')
            || str_contains($description, 'панел')
            || str_contains($description, 'горшки')
            || str_contains($description, 'горшок')
            || str_contains($description, 'вода')
            || str_contains($description, 'воду')
            || str_contains($description, 'накладк')
            || str_contains($description, 'эмаль')
            || str_contains($description, 'модуль')
            || str_contains($description, 'ацетилен')
            || str_contains($description, 'интернет')
            || str_contains($description, 'груз')
            || str_contains($description, 'зажим')
            || str_contains($description, 'крепеж')
            || str_contains($description, 'краск')
            || str_contains($description, 'муфт')
            || str_contains($description, 'коробк')
            || str_contains($description, 'ламинат')
            || str_contains($description, 'термостат')
            || str_contains($description, 'фитинг')
            || str_contains($description, 'насадк')
            || str_contains($description, 'герметик')
            || str_contains($description, 'полусфер')
            || str_contains($description, 'электротехнические товары')
            || str_contains($description, 'кирпич')
            || str_contains($description, 'водопотребление')
            || str_contains($description, 'вилатерм')
            || str_contains($description, 'песок')
            || str_contains($description, 'инструмент')
            || str_contains($description, 'крошку')
            || str_contains($description, 'крошка')
            || str_contains($description, 'спецодежд')
            || str_contains($description, 'товары')
            || str_contains($description, 'топливо')
            || str_contains($description, 'брус')
            || str_contains($description, 'извещатель')
            || str_contains($description, 'угольник')
            || str_contains($description, 'дюбель')
            || str_contains($description, 'диван')
            || str_contains($description, 'кресло')
            || str_contains($description, 'мебель')
            || str_contains($description, 'кувшин')
            || str_contains($description, 'продукцию')
            || str_contains($description, 'продукция')
            || str_contains($description, 'кабел')
            || str_contains($description, 'бахилы')
            || str_contains($description, 'фасонк')
            || str_contains($description, 'контейнер')
            || str_contains($description, 'фанер')
            || str_contains($description, 'рукосушилк')
            || str_contains($description, 'пленк')
            || str_contains($description, 'лент')
            || str_contains($description, 'растворител')
            || str_contains($description, 'диск')
            || str_contains($description, 'гранит')
            || str_contains($description, 'винт')
            || str_contains($description, 'клавишу')
            || str_contains($description, 'доводчик')
            || str_contains($description, 'грунтовк')
            || str_contains($description, 'наливной пол')
            || str_contains($description, 'перчатк')
            || str_contains($description, 'лист')
            || str_contains($description, 'доск')
            || str_contains($description, 'переход')
            || str_contains($description, 'плинтус')
            || str_contains($description, 'сверл')
            || str_contains($description, 'помп')
            || str_contains($description, 'лент')
            || str_contains($description, 'стойк')
            || str_contains($description, 'опор')
            || str_contains($description, 'драйвер')
            || str_contains($description, 'источник питания')
            || str_contains($description, 'провод')
            || str_contains($description, 'apple')
            || str_contains($description, 'фонар')
            || str_contains($description, 'флаг')
            || str_contains($description, 'шланг')
            || str_contains($description, 'отвод')
            || str_contains($description, 'техноакустик')
            || str_contains($description, 'заглушк')
            || str_contains($description, 'блок')
            || str_contains($description, 'зеркал')
            || str_contains($description, 'наливной состав')
            || str_contains($description, 'смес')
            || str_contains($description, 'головк')
            || str_contains($description, 'крышк')
            || str_contains($description, 'клей')
            || str_contains($description, 'ниппель')
            || str_contains($description, 'мастик')
            || str_contains($description, 'светильник')
            || str_contains($description, 'люк')
            || str_contains($description, 'ротор')
            || str_contains($description, 'щетк')
            || str_contains($description, 'выключател')
            || str_contains($description, 'гальк')
            || str_contains($description, 'пена')
            || str_contains($description, 'пену')
            || str_contains($description, 'метк')
            || str_contains($description, 'вал')
            || str_contains($description, 'прибор')
            || str_contains($description, 'изоляц')
            || str_contains($description, 'пеленк')
            || str_contains($description, 'фланец')
            || str_contains($description, 'турбин')
            || str_contains($description, 'гидрофобизатор')
            || str_contains($description, 'решетк')
            || str_contains($description, 'устройство')
            || str_contains($description, 'кран')
            || str_contains($description, 'резистор')
            || str_contains($description, 'ламп')
            || str_contains($description, 'барабан')
            || str_contains($description, 'смесител')
            || str_contains($description, 'датчик')
            || str_contains($description, 'мешки')
            || str_contains($description, 'мешок')
            || str_contains($description, 'бумагодержатель')
            || str_contains($description, 'смесь')
            || str_contains($description, 'гвозд')
            || str_contains($description, 'реагент')
            || str_contains($description, 'уголок')
            || str_contains($description, 'огнетушител')
            || str_contains($description, 'перегородк')
            || str_contains($description, 'сантехни')
            || str_contains($description, 'оборудование')
            || str_contains($description, 'огражден')
            || str_contains($description, 'экран')
            || str_contains($description, 'люк-двер')
            || str_contains($description, 'хозтов')
            || str_contains($description, 'цемент')
            || str_contains($description, 'топлив')
            || str_contains($description, 'круг')
            || str_contains($description, 'камин')
            || str_contains($description, 'леденц')
            || str_contains($description, 'аккум')
            || str_contains($description, 'ноутбук')
            || str_contains($description, 'хомут')
            || str_contains($description, 'шкаф')
            || str_contains($description, 'гкл')
            || str_contains($description, 'фильтр')
            || str_contains($description, 'счетчик')
            || str_contains($description, 'гипсокартон')
            || str_contains($description, 'молдинг')
            || str_contains($description, 'кислот')
            || str_contains($description, 'противоп. пен')
            || str_contains($description, 'комплект')
            || str_contains($description, 'шоколад')
            || str_contains($description, 'комплексные обеды')
            || str_contains($description, 'шлифкруг')
            || str_contains($description, 'стяжк')
            || str_contains($description, 'штукатурк')
            || str_contains($description, 'валик')
            || str_contains($description, 'шпаклевк')
            || str_contains($description, 'дросел')
            || str_contains($description, 'шпатлевк')
            || str_contains($description, 'лиценз')
            || str_contains($description, 'картридж')
            || str_contains($description, 'профил')
            || str_contains($description, 'гвлв')
            || str_contains($description, 'ввг')
            || str_contains($description, 'швеллер')
            || str_contains($description, 'гсп')
            || str_contains($description, 'креплен')
            || str_contains($description, 'эластомер')
            || str_contains($description, 'пропан')
            || str_contains($description, 'пеноплэкс')
            || str_contains($description, 'полибетол')
            || str_contains($description, 'насос')
            || str_contains($description, 'полумаск')
            || str_contains($description, 'рукав')
            || str_contains($description, 'ревизор')
            || str_contains($description, 'подоконник')
            || str_contains($description, 'шуруп')
            || str_contains($description, 'керамзит')
            || str_contains($description, 'средств')
            || str_contains($description, 'гель')
            || str_contains($description, 'антисептик')
            || str_contains($description, 'статор')
            || str_contains($description, 'подшипник')
            || str_contains($description, 'маркер')
            || str_contains($description, 'замк')
            || str_contains($description, 'электрод')
            || str_contains($description, 'гидроизоляц')
            || str_contains($description, 'воздуховод')
            || str_contains($description, 'теплопакет')
            || str_contains($description, 'конвектор')
        ) {
            $category = Payment::CATEGORY_MATERIAL;
        }

        return $category;
    }
}
