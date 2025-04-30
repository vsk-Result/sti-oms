<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('accrued_taxes.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index accrued-taxes') ? route('accrued_taxes.index') : null;
    $trail->parent('home');
    $trail->push('Начисленные налоги', $route);
});