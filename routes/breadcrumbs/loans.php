<?php

use App\Models\Loan;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('loans.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index loans') ? route('loans.index') : null;
    $trail->parent('home');
    $trail->push('Займы / Кредиты', $route);
});

Breadcrumbs::for('loans.create', function (BreadcrumbTrail $trail) {
    $trail->parent('loans.index');
    $trail->push('Новый займ / кредит');
});

Breadcrumbs::for('loans.edit', function (BreadcrumbTrail $trail, Loan $loan) {
    $trail->parent('loans.index', $loan);
    $trail->push('Изменение займа / кредита');
});
