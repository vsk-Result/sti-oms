<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('finance_report.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index finance-report') ? route('finance_report.index') : null;
    $trail->parent('home');
    $trail->push('Финансовый отчет', $route);
});

Breadcrumbs::for('finance_report.history.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index finance-report') ? route('finance_report.index') : null;
    $trail->parent('finance_report.index');
    $trail->push('История финансовых отчетов', $route);
});
