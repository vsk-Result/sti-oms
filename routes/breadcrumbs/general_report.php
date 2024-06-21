<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('general_report.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index finance-report') ? route('general_report.index') : null;
    $trail->parent('home');
    $trail->push('Отчет по общим затратам', $route);
});
