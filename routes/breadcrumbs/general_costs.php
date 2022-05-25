<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('general_costs.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index general-costs') ? route('general_costs.index') : null;
    $trail->parent('home');
    $trail->push('Распределение общих затрат', $route);
});
