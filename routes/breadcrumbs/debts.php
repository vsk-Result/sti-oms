<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('debts.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index debts') ? route('debts.index') : null;
    $trail->parent('home');
    $trail->push('Долги', $route);
});
