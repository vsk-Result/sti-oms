<?php

use App\Models\Debt\DebtImport;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('cron_processes.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index cron-processes') ? route('cron_processes.index') : null;
    $trail->parent('home');
    $trail->push('Статус фоновых процессов', $route);
});