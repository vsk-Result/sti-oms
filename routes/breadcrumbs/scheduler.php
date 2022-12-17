<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('scheduler.index', function (BreadcrumbTrail $trail) {
    $route = null;
    $trail->parent('home');
    $trail->push('Планировщик задач', $route);
});
