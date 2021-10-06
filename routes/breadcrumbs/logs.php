<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('logs.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index admin-logs') ? route('logs.index') : null;
    $trail->parent('home');
    $trail->push('Менеджер логов', $route);
});

Breadcrumbs::for('logs.show', function (BreadcrumbTrail $trail) {
    $trail->parent('logs.index');
    $trail->push('Просмотр лога');
});
