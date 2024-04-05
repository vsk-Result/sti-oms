<?php

namespace App\Models;

class FinanceReport
{
    public static function getPrognozFields(): array
    {
        return [
            'Зарплата и налоги для рабочих (11,8%)' => 'prognoz_zp_worker',
            'Зарплата и налоги для ИТР (6,6%)' => 'prognoz_zp_itr',
            'Покупка материала' => 'prognoz_material',
            'Оплата подрядчиков' => 'prognoz_podryad',
            'Общие расходы (8,20%)' => 'prognoz_general',
            'Услуги (5%)' => 'prognoz_service',
            'Консалтинг (Кемерово 11,39%)' => 'prognoz_consalting',
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
            'prognoz_consalting' => 0.1139
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
            'Расходы' => 'pay',
            'Сальдо без общ. Расходов' => 'balance',
            'Общие расходы' => 'general_balance',
            'Общие расходы / приходы %' => 'general_balance_to_receive_percentage',
            'Сальдо c общ. Расходами' => 'balance_with_general_balance',
            'Долг подрядчикам' => 'contractor_debt',
            'Долг подрядчикам за ГУ' => 'contractor_debt_gu',
            'Долг поставщикам' => 'provider_debt',
            'Долг за услуги' => 'service_debt',
            // 'Долг на зарплаты ИТР' => 'itr_salary_debt',
            'Долг на зарплаты рабочим' => 'workers_salary_debt',
            'Долг Заказчика за выпол.работы' => 'dolgZakazchikovZaVipolnenieRaboti',
            'Долг Заказчика за ГУ (фактич.удерж.)' => 'dolgFactUderjannogoGU',
            'Текущий Баланс объекта' => 'objectBalance',
            'Сумма договоров с Заказчиком' => 'contractsTotalAmount',
            'Остаток неотработанного аванса' => 'ostatokNeotrabotannogoAvansa',
            'Остаток к получ. от заказчика (в т.ч. ГУ)' => 'ostatokPoDogovoruSZakazchikom',
            'Прогнозируемые затраты ИТОГО' => 'prognoz_total',
        ];

        return array_merge(
            $infoFields,
            $prognozFields,
            [
                'Прогнозируемый Баланс объекта' => 'prognozBalance',
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
