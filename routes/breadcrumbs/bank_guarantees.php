<?php

use App\Models\BankGuarantee;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('bank_guarantees.index', function (BreadcrumbTrail $trail) {
    $route = auth()->user()->can('index bank-guarantees') ? route('bank_guarantees.index') : null;
    $trail->parent('home');
    $trail->push('Банковские гарантии и депозиты', $route);
});

Breadcrumbs::for('bank_guarantees.create', function (BreadcrumbTrail $trail) {
    $trail->parent('bank_guarantees.index');
    $trail->push('Новая банковская гарантия');
});

Breadcrumbs::for('bank_guarantees.show', function (BreadcrumbTrail $trail, BankGuarantee $guarantee) {
    $route = auth()->user()->can('show bank-guarantees') ? route('bank_guarantees.show', $guarantee) : null;
    $trail->parent('bank_guarantees.index');
    $trail->push('Банковская гарантия #' . $guarantee->id, $route);
});

Breadcrumbs::for('bank_guarantees.edit', function (BreadcrumbTrail $trail, BankGuarantee $guarantee) {
    $trail->parent('bank_guarantees.show', $guarantee);
    $trail->push('Изменение банковской гарантии');
});

Breadcrumbs::for('bank_guarantees.history.index', function (BreadcrumbTrail $trail) {
    $trail->parent('bank_guarantees.index');
    $trail->push('История БГ и депозитов');
});