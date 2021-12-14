<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('acts.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index acts') ? route('acts.index') : null;
    $trail->parent('home');
    $trail->push('Акты', $route);
});
