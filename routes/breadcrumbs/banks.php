<?php

use App\Models\Bank;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('banks.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index banks') ? route('banks.index') : null;
    $trail->parent('home');
    $trail->push('Банки', $route);
});

Breadcrumbs::for('banks.create', function (BreadcrumbTrail $trail) {
    $trail->parent('banks.index');
    $trail->push('Новый банк');
});

Breadcrumbs::for('banks.edit', function (BreadcrumbTrail $trail, Bank $bank) {
    $trail->parent('banks.index');
    $trail->push('Изменение банка');
});
