<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('objects.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index objects') ? route('objects.index') : null;
    $trail->parent('home');
    $trail->push('Объекты', $route);
});
