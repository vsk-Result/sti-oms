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

