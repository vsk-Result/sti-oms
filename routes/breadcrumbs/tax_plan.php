<?php

use App\Models\TaxPlanItem;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('tax_plan.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index tax_plan') ? route('tax_plan.index') : null;
    $trail->parent('home');
    $trail->push('План налогов к оплате', $route);
});

Breadcrumbs::for('tax_plan.create', function (BreadcrumbTrail $trail) {
    $trail->parent('tax_plan.index');
    $trail->push('Новая запись плана');
});

Breadcrumbs::for('tax_plan.edit', function (BreadcrumbTrail $trail, TaxPlanItem $item) {
    $trail->parent('tax_plan.index');
    $trail->push('Изменение записи плана');
});

Breadcrumbs::for('tax_plan.history.index', function (BreadcrumbTrail $trail) {
    $trail->parent('tax_plan.index');
    $trail->push('История плана налогов');
});