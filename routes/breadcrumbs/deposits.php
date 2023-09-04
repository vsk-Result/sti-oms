<?php

use App\Models\Deposit;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('deposits.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index deposits') ? route('deposits.index') : null;
    $trail->parent('home');
    $trail->push('Депозиты', $route);
});

Breadcrumbs::for('deposits.create', function (BreadcrumbTrail $trail) {
    $trail->parent('deposits.index');
    $trail->push('Новый депозит');
});

Breadcrumbs::for('deposits.show', function (BreadcrumbTrail $trail, Deposit $deposit) {
    $route = auth()->user()->can('show deposits') ? route('deposits.show', $deposit) : null;
    $trail->parent('deposits.index');
    $trail->push('Депозит #' . $deposit->id, $route);
});

Breadcrumbs::for('deposits.edit', function (BreadcrumbTrail $trail, Deposit $deposit) {
    $trail->parent('deposits.show', $deposit);
    $trail->push('Изменение депозита');
});

Breadcrumbs::for('deposits.history.index', function (BreadcrumbTrail $trail) {
    $trail->parent('deposits.index');
    $trail->push('История депозитов');
});