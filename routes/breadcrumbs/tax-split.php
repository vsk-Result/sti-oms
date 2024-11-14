<?php

use App\Models\TaxPlanItem;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('tax_split.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index tax_split') ? route('tax_split.index') : null;
    $trail->parent('home');
    $trail->push('Разбивка налогов', $route);
});
