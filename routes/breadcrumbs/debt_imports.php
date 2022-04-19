<?php

use App\Models\Debt\DebtImport;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('debt_imports.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index debt-imports') ? route('debt_imports.index') : null;
    $trail->parent('home');
    $trail->push('Загрузки долгов', $route);
});

Breadcrumbs::for('debt_imports.create', function (BreadcrumbTrail $trail) {
    $trail->parent('debt_imports.index');
    $trail->push('Загрузка долгов');
});
