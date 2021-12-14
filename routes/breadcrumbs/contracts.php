<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('contracts.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index contracts') ? route('contracts.index') : null;
    $trail->parent('home');
    $trail->push('Договора', $route);
});
