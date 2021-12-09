<?php

use App\Models\Debt\DebtImport;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('debt_imports.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index debt-imports') ? route('debt_imports.index') : null;
    $trail->parent('home');
    $trail->push('Загрузки долгов', $route);
});

Breadcrumbs::for('debt_imports.show', function (BreadcrumbTrail $trail, DebtImport $import) {
    $route = auth()->user()->can('show debt-imports') ? route('debt_imports.show', $import) : null;
    $trail->parent('debt_imports.index');
    $trail->push('Долги за ' . $import->getDateFormatted() . ' (' . $import->getType() . ')', $route);
});

Breadcrumbs::for('debt_imports.create', function (BreadcrumbTrail $trail) {
    $trail->parent('debt_imports.index');
    $trail->push('Загрузка долгов');
});
