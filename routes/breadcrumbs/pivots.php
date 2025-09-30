<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('pivots.debts.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Долги от СТИ');
});

Breadcrumbs::for('pivots.acts.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Долги к СТИ');
});

Breadcrumbs::for('pivots.balances.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Отчет по балансам');
});

Breadcrumbs::for('pivots.dtsti.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Долги ДТ - СТИ');
});

Breadcrumbs::for('pivots.cash_flow.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Отчет CASH FLOW');
});

Breadcrumbs::for('pivots.acts_category.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Отчет по категориям');
});

Breadcrumbs::for('pivots.money_movement.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Отчет о движении денежных средств');
});

Breadcrumbs::for('pivots.residence.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Отчет о проживании');
});

Breadcrumbs::for('pivots.calculate_workers_cost.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index calculate-workers-cost') ? route('pivots.calculate_workers_cost.index') : null;
    $trail->parent('home');
    $trail->push('Расчет стоимости рабочих', $route);
});

Breadcrumbs::for('pivots.organization_debts.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Отчет по долгам контрагентов');
});
