<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('roles.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index admin-roles') ? route('roles.index') : null;
    $trail->parent('home');
    $trail->push('Роли доступа', $route);
});

Breadcrumbs::for('roles.create', function (BreadcrumbTrail $trail) {
    $trail->parent('roles.index');
    $trail->push('Новая роль');
});

Breadcrumbs::for('roles.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('roles.index');
    $trail->push('Изменение роли');
});
