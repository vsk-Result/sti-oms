<?php

use App\Models\Loan;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('loans.history.index', function (BreadcrumbTrail $trail, Loan $loan) {
    $route = auth()->user()->can('index loans') ? route('loans.history.index', $loan) : null;
    $trail->parent('loans.index');
    $trail->push('История оплат для ' . $loan->name . ' (' . $loan->getType() . ')', $route);
});

Breadcrumbs::for('loans.history.create', function (BreadcrumbTrail $trail, Loan $loan) {
    $trail->parent('loans.history.index', $loan);
    $trail->push('Новая оплата для ' . $loan->name . ' (' . $loan->getType() . ')');
});

Breadcrumbs::for('loans.history.edit', function (BreadcrumbTrail $trail, Loan $loan) {
    $trail->parent('loans.history.index', $loan);
    $trail->push('Изменение оплаты для ' . $loan->name . ' (' . $loan->getType() . ')');
});
