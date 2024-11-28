<?php

use App\Models\CashCheck\CashCheck;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Проверка касс CRM

Breadcrumbs::for('crm_cash_check.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index crm-cash-check') ? route('crm_cash_check.index') : null;
    $trail->parent('home');
    $trail->push('Проверка касс CRM', $route);
});

Breadcrumbs::for('crm_cash_check.show', function (BreadcrumbTrail $trail, CashCheck $check) {
    $trail->parent('crm_cash_check.index');
    $trail->push('Детализация кассы ' . $check->crmCost->name . ' закрытого периода ' . $check->getFormattedPeriod());
});
