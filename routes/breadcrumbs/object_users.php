<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('object_users.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index admin-roles') ? route('objects.users.index') : null;
    $trail->parent('home');
    $trail->push('Доступ к объектам', $route);
});

Breadcrumbs::for('object_users.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('object_users.index');
    $trail->push('Изменение доступа');
});
