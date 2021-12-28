<?php

use App\Models\Payment;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('payments.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index payments') ? route('payments.index') : null;
    $trail->parent('home');
    $trail->push('Оплаты', $route);
});

Breadcrumbs::for('payments.create', function (BreadcrumbTrail $trail) {
    $trail->parent('payments.index');
    $trail->push('Загрузить оплату');
});

Breadcrumbs::for('payments.show', function (BreadcrumbTrail $trail, Payment $payment) {
    $route = auth()->user()->can('show payments') ? route('payments.show', $payment) : null;
    $trail->parent('payments.index');
    $trail->push('Оплата за ' . $payment->getDateFormatted(), $route);
});

Breadcrumbs::for('payments.edit', function (BreadcrumbTrail $trail, Payment $payment) {
    $trail->parent('payments.show', $payment);
    $trail->push('Изменение оплаты');
});

Breadcrumbs::for('payments.history.index', function (BreadcrumbTrail $trail) {
    $trail->parent('payments.index');
    $trail->push('История оплат');
});
