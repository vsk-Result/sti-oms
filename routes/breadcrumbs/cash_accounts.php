<?php

use App\Models\CashAccount\CashAccount;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('cash_accounts.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index cash-accounts') ? route('cash_accounts.index') : null;
    $trail->parent('home');
    $trail->push('Кассы', $route);
});

Breadcrumbs::for('cash_accounts.create', function (BreadcrumbTrail $trail) {
    $trail->parent('cash_accounts.index');
    $trail->push('Новая касса');
});

Breadcrumbs::for('cash_accounts.show', function (BreadcrumbTrail $trail, CashAccount $cashAccount) {
    $route = auth()->user()->can('show cash-accounts') ? route('cash_accounts.show', $cashAccount) : null;
    $trail->parent('cash_accounts.index');
    $trail->push('Касса ' . $cashAccount->name, $route);
});

Breadcrumbs::for('cash_accounts.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('cash_accounts.index');
    $trail->push('Изменение кассы');
});

Breadcrumbs::for('cash_accounts.payments.index', function (BreadcrumbTrail $trail, CashAccount $cashAccount) {
    $route = auth()->user()->can('index cash-accounts') ? route('cash_accounts.payments.index', $cashAccount) : null;
    $trail->parent('cash_accounts.index');
    $trail->push('Записи кассы ' . $cashAccount->name, $route);
});
