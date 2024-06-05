<?php

namespace App\Models;

class FinanceReport
{
    public static function getPrognozConsaltingPercentForObject(string $objectCode): float
    {
        return [
            '361' => 0.1139,
            '365' => 0.1,
        ][$objectCode] ?? 0;
    }

    public static function getPrognozFields(): array
    {
        return [
            'Зарплата и налоги для рабочих (11,8%)' => 'prognoz_zp_worker',
            'Зарплата и налоги для ИТР (6,6%)' => 'prognoz_zp_itr',
            'Покупка материала' => 'prognoz_material',
            'Оплата подрядчиков' => 'prognoz_podryad',
            'Общие расходы (8,20%)' => 'prognoz_general',
            'Услуги (5%)' => 'prognoz_service',
            'Консалтинг' => 'prognoz_consalting',
        ];
    }

    public static function getPercentForField(string $field): float
    {
        return [
            'prognoz_zp_worker' => 0.118,
            'prognoz_zp_itr' => 0.066,
            'prognoz_material' => 0,
            'prognoz_podryad' => 0,
            'prognoz_general' => 0.082,
            'prognoz_service' => 0.05,
            'prognoz_consalting' => 0
        ][$field];
    }

    public static function getNameForField(string $field): string
    {
        return array_search($field, self::getPrognozFields());
    }

    public static function getInfoFields(): array
    {
        $prognozFields = self::getPrognozFields();
        $infoFields = [
            'Приходы' => 'receive',
            'Заказчик' => 'receive_customer',
            'Фиксированный аванс' => 'receive_customer_fix_avans',
            'Целевой аванс' => 'receive_customer_target_avans',
            'Акты' => 'receive_customer_acts',
            'Гарантийное удержание' => 'receive_customer_gu',
            'Ретробонусы ДТГ' => 'receive_retro_dtg',
            'Прочие' => 'receive_other',
            'Расходы' => 'pay',
            'Наличные ' => 'pay_cash',
            'Безналичные' => 'pay_non_cash',
            'Сальдо без общ. Расходов' => 'balance',
            'Общие расходы' => 'general_balance',
            'Услуги по трансферу' => 'transfer_service',
            'Расходы офиса, налоги' => 'office_service',
            'Общие расходы / приходы %' => 'general_balance_to_receive_percentage',
            'Сальдо c общ. Расходами' => 'balance_with_general_balance',
            'Долг подрядчикам' => 'contractor_debt',
            'Долг подрядчикам за ГУ' => 'contractor_debt_gu',
            'Долг поставщикам' => 'provider_debt',
            'Долг за услуги' => 'service_debt',
            // 'Долг на зарплаты ИТР' => 'itr_salary_debt',
            'Долг на зарплаты рабочим' => 'workers_salary_debt',
            'Долг по налогам' => 'tax_debt',
            'Долги ИТОГО' => 'total_debts',
            'Долг Заказчика за выпол.работы' => 'dolgZakazchikovZaVipolnenieRaboti',
            'Долг Заказчика за ГУ (фактич.удерж.)' => 'dolgFactUderjannogoGU',
            'Долг Заказчика' => 'customer_debts',
            'Текущий Баланс объекта' => 'objectBalance',
            'Сумма договоров с Заказчиком' => 'contractsTotalAmount',
            'Остаток неотработанного аванса' => 'ostatokNeotrabotannogoAvansa',
            'Остаток неотработанного аванса в твердой цене' => 'ostatokNeotrabotannogoAvansaFix',
            'Остаток неотработанного аванса в изменяемой цене' => 'ostatokNeotrabotannogoAvansaFloat',
            'Остаток к получ. от заказчика (в т.ч. ГУ)' => 'ostatokPoDogovoruSZakazchikom',
            'Прогнозируемые затраты ИТОГО' => 'prognoz_total',
        ];

        return array_merge(
            $infoFields,
            $prognozFields,
            [
                'Прогнозируемый Баланс объекта' => 'prognozBalance',
                'Планируемая Рентабельность' => 'planProfitability',
                'Планируемая Рентабельность (материал)' => 'planProfitability_material',
                'Планируемая Рентабельность (работы)' => 'planProfitability_rad',
                '% времени' => 'time_percent',
                '% выполнения' => 'complete_percent',
                '% денег' => 'money_percent',
                'Плановая готовность %' => 'plan_ready_percent',
                'Фактическая готовность %' => 'fact_ready_percent',
                'Отклонение от плана %' => 'deviation_plan_percent',
            ]
        );
    }
}
