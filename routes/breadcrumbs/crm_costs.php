<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('crm_costs.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index crm-costs') ? route('crm_costs.index') : null;
    $trail->parent('home');
    $trail->push('Статус касс CRM', $route);
});
