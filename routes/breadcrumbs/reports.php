<?php

use App\Models\Payment;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('reports.index', function (BreadcrumbTrail $trail) {
    $route = null;
    $trail->parent('home');
    $trail->push('Отчеты', $route);
});

Breadcrumbs::for('reports.all_reports.index', function (BreadcrumbTrail $trail) {
    $route = null;
    $trail->parent('home');
    $trail->push('Свот отчетов', $route);
});
