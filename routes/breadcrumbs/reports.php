<?php

use App\Models\Payment;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('reports.index', function (BreadcrumbTrail $trail) {
    $route = null;
    $trail->parent('home');
    $trail->push('Отчеты', $route);
});

Breadcrumbs::for('reports.itr_salary_object.create', function (BreadcrumbTrail $trail) {
    $trail->parent('reports.index');
    $trail->push('Отчет по расходам на ЗП ИТР по проектам');
});
