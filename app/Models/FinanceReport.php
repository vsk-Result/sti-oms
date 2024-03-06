<?php

namespace App\Models;

class FinanceReport
{
    public static function getInfoFields(): array
    {
        return [
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
            'Прогнозируемый Баланс объекта' => 'prognozBalance',
        ];
    }
}
