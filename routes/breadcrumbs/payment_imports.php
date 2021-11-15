<?php

use App\Models\PaymentImport;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('payment_imports.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index payment-imports') ? route('payment_imports.index') : null;
    $trail->parent('home');
    $trail->push('Загрузки оплат', $route);
});

Breadcrumbs::for('payment_imports.show', function (BreadcrumbTrail $trail, PaymentImport $import) {
    $route = auth()->user()->can('show payment-imports') ? route('payment_imports.show', $import) : null;
    $trail->parent('payment_imports.index');
    $trail->push('Оплаты за ' . $import->getDateFormatted() . ' (' . $import->getType() . ')', $route);
});

Breadcrumbs::for('payment_imports.edit', function (BreadcrumbTrail $trail, PaymentImport $import) {
    $trail->parent('payment_imports.show', $import);
    $trail->push('Изменение оплат за ' . $import->getDateFormatted() . ' (' . $import->getType() . ')');
});

Breadcrumbs::for('payment_imports.types.statements.create', function (BreadcrumbTrail $trail) {
    $trail->parent('payment_imports.index');
    $trail->push('Загрузка оплат из выписки');
});

Breadcrumbs::for('payment_imports.types.crm_cost_closures.create', function (BreadcrumbTrail $trail) {
    $trail->parent('payment_imports.index');
    $trail->push('Загрузка оплат из кассы CRM');
});

Breadcrumbs::for('payment_imports.types.history.create', function (BreadcrumbTrail $trail) {
    $trail->parent('payment_imports.index');
    $trail->push('Загрузка из истории оплат');
});
