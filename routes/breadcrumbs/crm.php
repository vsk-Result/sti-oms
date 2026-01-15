<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Статус касс CRM

Breadcrumbs::for('crm_costs.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index crm-costs') ? route('crm_costs.index') : null;
    $trail->parent('home');
    $trail->push('Статус касс', $route);
});

// Статус переноса оплат на карты из CRM

Breadcrumbs::for('crm.avanses.imports.split.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index crm-split-avans-imports') ? route('crm.avanses.imports.split.index') : null;
    $trail->parent('home');
    $trail->push('Статус переноса оплат на карты из CRM', $route);
});
